<?php

namespace fvucemilo\phpmvc\DB\Migrations;

use Exception;
use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\DB\Database;
use PDO;

/**
 * The MigrationManager class provides methods that manages database migrations.
 */
class MigrationManager
{
    /**
     * @param array $dbConfig An array containing the database configuration parameters.
     *   The array should include the following keys:
     *   - dsn: The database connection string.
     *   - user: The username used to connect to the database.
     *   - password: The password used to connect to the database.
     *   - default_migration_table: (Optional) The name of the table used to store migration information.
     *     Defaults to 'migrations'.
     */
    public array $dbConfig;

    /**
     * @var PDO Represents a connection between PHP and a database server.
     */
    protected PDO $pdo;

    /**
     * MigrationManager constructor.
     *
     * @param PDO $pdo Represents a connection between PHP and a database server.
     * @param array $dbConfig An array containing the database configuration parameters.
     *   The array should include the following keys:
     *   - dsn: The database connection string.
     *   - user: The username used to connect to the database.
     *   - password: The password used to connect to the database.
     *   - default_migration_table: (Optional) The name of the table used to store migration information.
     *     Defaults to 'migrations'.
     *
     * @return void
     */
    public function __construct(PDO $pdo, array $dbConfig)
    {
        $this->pdo = $pdo;
        $this->$dbConfig = $dbConfig;
    }

    /**
     * Applies any new migrations that have not yet been applied to the database.
     *
     * @param string $engine The database engine to use.
     *
     * @return void
     */
    public function applyMigrations(string $engine): void
    {
        $tableName = $this->$dbConfig['migrations']['default_migration_table'] ?? 'migrations';
        $this->createMigrationsTable($tableName, $engine);
        $appliedMigrations = $this->getAppliedMigrations($tableName);
        $newMigrations = [];
        $migrationsPath = $this->dbConfig['migrations']['migrations_path'] ?? '/migrations';
        $files = scandir(Application::$ROOT_DIR . $migrationsPath);
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;
            require_once Application::$ROOT_DIR . $migrationsPath . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migrations '$migration'.");
            $instance->up();
            $this->log("Applied migrations '$migration'.");
            $newMigrations[] = $migration;
        }
        if (!empty($newMigrations)) $this->saveMigrations($newMigrations, $tableName); else $this->log("There are no migrations to apply.");
    }

    /**
     * Creates the migrations table if it does not already exist.
     *
     *
     * @param string $tableName The name of the migrations table.
     * @param string $engine The database engine to use.
     *
     * @return void
     */
    protected function createMigrationsTable(string $tableName, string $engine): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migrations VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=$engine;");
    }

    /**
     * Retrieves a list of migrations that have already been applied to the database.
     *
     * @param string $tableName The name of the migrations table.
     *
     * @return array|false An array of migration names or false if there was an error.
     */
    protected function getAppliedMigrations(string $tableName): array|false
    {
        $statement = $this->pdo->prepare("SELECT migrations FROM $tableName");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Logs a message to the console.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    protected function log(string $message): void
    {
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
    }

    /**
     * Saves a list of new migrations to the migrations table in the database.
     *
     * @param array $newMigrations An array of migration names.
     * @param string $tableName The name of the migrations table.
     *
     * @return void
     */
    protected function saveMigrations(array $newMigrations, string $tableName): void
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $newMigrations));
        $statement = $this->pdo->prepare("INSERT INTO $tableName (migrations) VALUES $str");
        $statement->execute();
    }

    /**
     * Reverses all previously applied migrations in reverse order and drops the migrations table.
     *
     * @return void
     *
     * @throws Exception If the DSN is empty, an exception with status code 500 is thrown.
     */
    public function undoMigrations(): void
    {
        $dsn = $this->dbConfig['dsn'];
        $databaseName = Database::getDatabaseName($dsn);
        $tableName = $this->$dbConfig['migrations']['default_migration_table'] ?? 'migrations';
        $tableEngineName = Database::getTableEngineName($databaseName, $tableName);
        $this->createMigrationsTable($tableName, $tableEngineName);
        $appliedMigrations = $this->getAppliedMigrations($tableName);
        $droppedMigrations = [];
        $migrationsPath = $this->dbConfig['migrations']['migrations_path'] ?? '/migrations';
        $files = scandir(Application::$ROOT_DIR . $migrationsPath);
        $toDropMigrations = array_intersect($appliedMigrations, $files);
        if (empty($toDropMigrations)) {
            $this->log("There are no migrations to undo.");
            return;
        }
        rsort($toDropMigrations);
        $droppedMigrations = $this->getDroppedMigrations($toDropMigrations, $droppedMigrations);
        $this->removeDroppedMigrations($droppedMigrations, $tableName);
        $this->dropMigrationsTable($tableName);
        $this->log("Dropped migrations table '$tableName'.");
    }

    /**
     * Reverses and records migrations that have been dropped.
     *
     * @param array $toDropMigrations An array of migration file names to be undone.
     * @param array $droppedMigrations An array of migration file names that have been undone in previous runs.
     *
     * @return array An array of migration file names that have been undone, including those from the current run.
     */
    protected function getDroppedMigrations(array $toDropMigrations, array $droppedMigrations): array
    {
        foreach ($toDropMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;
            $migrationsPath = $this->dbConfig['migrations']['migrations_path'] ?? '/migrations';
            require_once Application::$ROOT_DIR . $migrationsPath . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Undoing migrations '$migration'.");
            $instance->down(); // Reverse the migration by calling the "down" method
            $this->log("Undid migrations '$migration'.");
            $droppedMigrations[] = $migration;
        }
        return $droppedMigrations;
    }

    /**
     * Removes the specified dropped migrations from the migrations table.
     *
     * @param array $droppedMigrations An array of migration file names that have been dropped.
     * @param string $tableName The name of the migrations table.
     *
     * @return void
     */
    protected function removeDroppedMigrations(array $droppedMigrations, string $tableName): void
    {
        if (!empty($droppedMigrations)) {
            $str = implode(',', array_map(fn($m) => "'$m'", $droppedMigrations));
            $statement = $this->pdo->prepare("DELETE FROM $tableName WHERE migrations IN ($str)");
            $statement->execute();
            $this->log("Dropped migrations: " . implode(', ', $droppedMigrations));
        }
    }

    /**
     * Drops the migrations table if it exists.
     *
     * @param string $tableName The name of the migrations table.
     *
     * @return void
     */
    protected function dropMigrationsTable(string $tableName): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS $tableName");
    }

    /**
     * Undo the last migration that was applied.
     *
     * @return void
     */
    public function undoLastMigration(): void
    {
        $tableName = $this->$dbConfig['migrations']['default_migration_table'] ?? 'migrations';
        $lastMigration = $this->getLastAppliedMigration($tableName);
        if (empty($lastMigration)) {
            $this->log("There is no migration tu undo.");
            return;
        }
        $migration = $lastMigration['migrations'];
        $migrationsPath = $this->dbConfig['migrations']['migrations_path'] ?? '/migrations';
        require_once Application::$ROOT_DIR . $migrationsPath . $migration;
        $className = pathinfo($migration, PATHINFO_FILENAME);
        $instance = new $className();
        $this->log("Undoing last migration '$migration'.");
        $instance->down();
        $this->log("Undone last migration '$migration'.");
        $statement = $this->pdo->prepare("DELETE FROM  WHERE id = ?");
        $statement->execute([$lastMigration['id']]);
    }

    /**
     * Retrieves the last applied migration from the migrations table.
     *
     * @param string $tableName The name of the migrations table.
     *
     * @return array|null The last applied migration or null if there are none.
     */
    protected function getLastAppliedMigration(string $tableName): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM $tableName ORDER BY created_at DESC LIMIT 1");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
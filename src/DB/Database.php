<?php

namespace fvucemilo\phpmvc\DB;

use Exception;
use fvucemilo\phpmvc\DB\Migrations\MigrationManager;
use PDO;
use PDOStatement;

/**
 * The Database class represents a connection between PHP and a database server.
 */
class Database
{
    /**
     * @var PDO Represents a connection between PHP and a database server.
     */
    public PDO $pdo;

    /**
     * @var MigrationManager The MigrationManager class provides methods that manages database migrations.
     */
    public MigrationManager $migrationManager;

    /**
     * Database constructor.
     *
     * @param array $dbConfig An array containing the database configuration parameters.
     *   The array should include the following keys:
     *   - dsn: The database connection string.
     *   - user: The username used to connect to the database.
     *   - password: The password used to connect to the database.
     *   - default_migration_table: (Optional) The name of the table used to store migration information.
     *     Defaults to 'migrations'.
     */
    public function __construct(array $dbConfig = [])
    {
        $dbDsn = $dbConfig['dsn'] ?? '';
        $username = $dbConfig['user'] ?? '';
        $password = $dbConfig['password'] ?? '';

        $this->pdo = new PDO($dbDsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->migrationManager = new MigrationManager($this->pdo, $dbConfig);
    }

    /**
     * Extracts the database name from a DSN string.
     *
     * @param string $dsn The database DSN string.
     *
     * @return string The name of the database parsed from the DSN.
     *
     * @throws Exception If the DSN is empty, an exception with status code 500 is thrown.
     */
    public static function getDatabaseName(string $dsn): string
    {
        return !empty($dsn)
            ? trim(parse_url($dsn)['path'], '/')
            : throw new Exception("Internal server error", 500);
    }

    /**
     * Retrieves the storage engine used by a table in a given database.
     *
     * @param string $databaseName The name of the database.
     * @param string $tableName The name of the table.
     *
     * @return string The name of the storage engine.
     */
    public static function getTableEngineName(string $databaseName, string $tableName): string
    {
        $statement = (new Database)->prepare("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = $databaseName AND TABLE_NAME = $tableName");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC)['ENGINE'];
    }

    /**
     * Prepares a SQL statement for execution and returns a PDOStatement object.
     *
     * @param string $sql The SQL statement to prepare for execution.
     *
     * @return PDOStatement Returns a PDOStatement object representing the prepared statement.
     */
    public function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }
}
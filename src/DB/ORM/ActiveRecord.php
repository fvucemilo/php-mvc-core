<?php

namespace fvucemilo\phpmvc\DB\ORM;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\MVC\Models\Model;
use PDO;

/**
 * The ActiveRecord class provides an implementation of the Active Record pattern for managing database records.
 */
abstract class ActiveRecord extends Model implements ActiveRecordInterface
{
    /**
     * Returns all records from the database table associated with this model.
     *
     * @param array|null $where An associative array of conditions to match against, or null to include no conditions.
     *
     * @return static[] An array of ActiveRecord instances representing the found records.
     */
    public static function find(?array $where = null): array
    {
        $tableName = self::tableName();
        $whereClause = "";
        $params = [];
        if ($where !== null) list($whereClause, $params) = self::buildWhereClause($where);
        $statement = Application::$app->db->prepare("SELECT * FROM $tableName $whereClause");
        foreach ($params as $key => $value) $statement->bindValue($key, $value);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Returns the name of the database table associated with this model.
     *
     * @return string The name of the database table.
     */
    abstract public static function tableName(): string;

    /**
     * Creates a WHERE clause and parameter array based on the given conditions.
     *
     * @param array|null $where An associative array of conditions to match against, or null to include no conditions.
     *
     * @return array An array containing the WHERE clause and parameter array to use in a database query.
     */
    private static function buildWhereClause(?array $where): array
    {
        $whereClause = "";
        $params = [];
        if ($where !== null) {
            $attributes = array_keys($where);
            $whereClause = " WHERE " . implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
            foreach ($where as $key => $value) $params[":$key"] = $value;
        }
        return array($whereClause, $params);
    }

    /**
     * Finds a single record from the database that matches the specified conditions.
     *
     * @param array|null $where An associative array of conditions to match against, or null to include no conditions.
     *
     * @return static|null The ActiveRecord instance representing the found record, or null if no record was found.
     */
    public static function findOne(?array $where = null): ?static
    {
        $tableName = static::tableName();
        $whereClause = "";
        $params = [];
        if ($where !== null) list($whereClause, $params) = self::buildWhereClause($where);
        $statement = Application::$app->db->prepare("SELECT * FROM $tableName $whereClause LIMIT 1");
        foreach ($params as $key => $value) $statement->bindValue($key, $value);
        $statement->execute($params);
        return $statement->fetchObject(static::class) ?: null;
    }

    /**
     * Returns the result of an aggregate function on the specified attribute.
     *
     * @param string $function The aggregate function to apply, e.g. "COUNT", "SUM", "AVG", "MAX", or "MIN".
     * @param string $attribute The name of the attribute to aggregate.
     * @param array|null $where An associative array of conditions to match against, or null to include no conditions.
     *
     * @return mixed The result of the aggregate function.
     */
    public static function aggregate(string $function, string $attribute, ?array $where = null): mixed
    {
        $tableName = static::tableName();
        $whereClause = "";
        $params = [];
        if ($where !== null) list($whereClause, $params) = self::buildWhereClause($where);
        $statement = Application::$app->db->prepare("SELECT $function($attribute) FROM $tableName $whereClause");
        foreach ($params as $key => $value) $statement->bindValue($key, $value);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Saves the current record to the database.
     *
     * @return bool True if the record was saved successfully, false otherwise.
     */
    public function save(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = Application::$app->db->prepare("INSERT INTO $tableName (" . implode(",", $attributes) . ") 
                VALUES (" . implode(",", $params) . ")");
        foreach ($attributes as $attribute) $statement->bindValue(":$attribute", $this->{$attribute});
        $statement->execute();
        return true;
    }

    /**
     * Saves all records in the provided array to the database.
     *
     * @param ActiveRecord[] $records The records to save.
     *
     * @return bool True if all records were saved successfully, false otherwise.
     */
    public function saveAll(array $records): bool
    {
        if (empty($records)) return false;
        $tableName = $this->tableName();
        $attributes = $records[0]->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $values = [];
        foreach ($records as $ignored) $values[] = "(" . implode(",", $params) . ")";
        $statement = Application::$app->db->prepare("INSERT INTO $tableName (" . implode(",", $attributes) . ")
                VALUES " . implode(",", $values));
        foreach ($records as $record) foreach ($attributes as $attribute) $statement->bindValue(":$attribute", $record->{$attribute});
        return $statement->execute();
    }

    /**
     * Deletes the current record from the database.
     *
     * @return bool True if the record was deleted successfully, false otherwise.
     */
    public function delete(): bool
    {
        $tableName = $this->tableName();
        $primaryKey = static::getId();
        $statement = Application::$app->db->prepare("DELETE FROM $tableName WHERE $primaryKey = :id");
        $statement->bindValue(":id", $this->{$primaryKey});
        return $statement->execute();
    }

    /**
     * Returns the name of the primary key attribute for this model.
     *
     * @return string The name of the primary key attribute.
     */
    public static function getId(): string
    {
        return 'id';
    }

    /**
     * Deletes all records from the database table associated with this model that match the specified conditions.
     *
     * @param array|null $where An associative array of conditions to match against, or null to delete all records.
     *
     * @return bool True if the records were deleted successfully, false otherwise.
     */
    public function deleteAll(?array $where = null): bool
    {
        $tableName = $this->tableName();
        $whereClause = "";
        $params = [];
        if ($where !== null) list($whereClause, $params) = self::buildWhereClause($where);
        $statement = Application::$app->db->prepare("DELETE FROM $tableName $whereClause");
        foreach ($params as $key => $value) $statement->bindValue($key, $value);
        $statement->execute($params);
        return $statement->rowCount() > 0;
    }
}
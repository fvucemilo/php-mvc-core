<?php

namespace fvucemilo\phpmvc\DB\ORM;

/**
 * This interface defines the methods required for implementing an ActiveRecord pattern.
 */
interface ActiveRecordInterface
{
    /**
     * Returns the name of the database table associated with this ActiveRecord.
     *
     * @return string The name of the database table.
     */
    public static function tableName(): string;

    /**
     * Returns the name of the ID column for this ActiveRecord.
     *
     * @return string The name of the ID column.
     */
    public static function getId(): string;

    /**
     * Finds and returns an array of ActiveRecord objects that match the given conditions.
     *
     * @param array|null $where An array of conditions to filter the results by.
     *
     * @return array An array of ActiveRecord objects.
     */
    public static function find(?array $where = null): array;

    /**
     * Finds and returns a single ActiveRecord object that matches the given conditions.
     *
     * @param array|null $where An array of conditions to filter the results by.
     *
     * @return static|null An instance of the ActiveRecord object or null if no object is found.
     */
    public static function findOne(?array $where = null): ?static;

    /**
     * Performs an aggregate function on the specified attribute for the ActiveRecord objects that match the given conditions.
     *
     * @param string $function The aggregate function to perform (e.g. 'SUM', 'AVG', 'MAX', 'MIN').
     * @param string $attribute The name of the attribute to perform the function on.
     * @param array|null $where An array of conditions to filter the records to be aggregated.
     *
     * @return mixed The result of the aggregate function.
     */
    public static function aggregate(string $function, string $attribute, ?array $where = null): mixed;

    /**
     * Saves the current ActiveRecord object to the database.
     *
     * @return bool True if the save was successful, false otherwise.
     */
    public function save(): bool;

    /**
     * Saves an array of ActiveRecord objects to the database.
     *
     * @param array $records An array of ActiveRecord objects to save.
     *
     * @return bool True if the save was successful, false otherwise.
     */
    public function saveAll(array $records): bool;

    /**
     * Deletes the current ActiveRecord object from the database.
     *
     * @return bool True if delete was successful, false otherwise.
     */
    public function delete(): bool;

    /**
     * Deletes ActiveRecord objects from the database that match the given conditions.
     *
     * @param array|null $where An array of conditions to filter the records to be deleted.
     *
     * @return bool True if delete was successful, false otherwise.
     */
    public function deleteAll(?array $where = null): bool;
}
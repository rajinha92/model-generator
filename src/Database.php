<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 29/11/16
 * Time: 10:26
 */

namespace Rafael\ModelGenerator;

use Rafael\ModelGenerator\Contracts\Database as DatabaseContract;

class Database implements DatabaseContract
{
    /**
     * Database name
     * @var string
     */
    protected $database;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->database = config('database.connections.' . config('database.default') . '.database');
    }

    /**
     * return an array with all tables in the database
     * @return array
     */
    public function getTables() : array
    {
        $tables = \DB::select("SELECT TABLE_NAME, CREATE_TIME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ORDER BY CREATE_TIME", [$this->database]);
        return $tables;
    }

    /**
     * return an array with all columns in the $table
     * @param string $table
     * @return array
     */
    public function getColumns($table) : array
    {
        $columns = \DB::select(
            "
                SELECT
                    TABLE_SCHEMA,
                    TABLE_NAME,
                    COLUMN_NAME,
                    ORDINAL_POSITION,
                    COLUMN_DEFAULT,
                    IS_NULLABLE,
                    DATA_TYPE,
                    CHARACTER_MAXIMUM_LENGTH,
                    NUMERIC_PRECISION,
                    NUMERIC_SCALE,
                    COLUMN_TYPE,
                    COLUMN_KEY,
                    EXTRA
                FROM
                  INFORMATION_SCHEMA.COLUMNS
                WHERE
                  TABLE_NAME = ?
                AND TABLE_SCHEMA = ?
            ",
            [$table, $this->database]
        );

        return $columns;
    }

    /**
     * return an array with all unique keys within the $columns given
     * @param array $columns
     * @return array
     */
    public function getUniqueKeys(array $columns) : array
    {
        return array_filter($columns, function ($col)
        {
            return $col["COLUMN_KEY"] == "UNI";
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * return an array with all foreign keys specs for $foreignKeys array
     * @param array $foreignKeys
     * @return array
     */
    public function getForeignKeys(array $foreignKeys) : array
    {
        if (!count($foreignKeys))
            return [];

        $foreignSpecs = \DB::select(
            "
                SELECT
                    CONSTRAINT_NAME, TABLE_SCHEMA, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM
                  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                  COLUMN_NAME IN (?)
                  AND TABLE_SCHEMA = ?
            "
            , [implode(",", $foreignKeys), $this->database]);

        return $foreignSpecs;
    }
}
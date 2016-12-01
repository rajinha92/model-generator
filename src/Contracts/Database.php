<?php
/**
 * Author: Rafael Conrado
 * Date: 29/11/16
 */

namespace Rafael\ModelGenerator\Contracts;

interface Database
{

    /**
     * return an array with all tables in the database
     * @return array
     */
    public function getTables() : array;

    /**
     * return an array with all columns in the $table
     * @param string $table
     * @return array
     */
    public function getColumns($table) : array;

    /**
     * return an array with all unique keys within the $columns given
     * @param array $columns
     * @return array
     */
    public function getUniqueKeys(array $columns) : array;

    /**
     * return an array with all foreign keys specs for $foreignKeys array
     * @param array $foreignKeys
     * @return array
     */
    public function getForeignKeys(array $foreignKeys) : array;

}
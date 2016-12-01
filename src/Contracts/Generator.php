<?php

/**
 * Author: Rafael Conrado
 * Date: 29/11/16
 */

namespace Rafael\ModelGenerator\Contracts;

interface Generator
{

    /**
     * Make file
     * @param $table_name
     * @param array $columns
     * @param array $options
     * @return bool
     */
    public function make($table_name, array $columns, array $options = []) : bool;

}
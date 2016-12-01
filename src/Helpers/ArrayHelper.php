<?php

namespace Rafael\ModelGenerator\Helpers;

class ArrayHelper
{

    /**
     * return an one direction array applying the $callback function
     * @param array $array
     * @param $column_name
     * @param callable $callback
     * @return array
     */
    public function get_filtered_column(array $array, $column_name, callable $callback) : array
    {
        return array_column(
            array_filter($array, $callback),
            $column_name
        );
    }


    /**
     * search for a key in an array of objects
     * @param $key
     * @param array $arrayOfObjects
     * @return bool
     */
    public function object_key_exists($key, array $arrayOfObjects) : bool
    {
        for ($i = 0; $i < count($arrayOfObjects); $i++)
        {
            if (is_object($arrayOfObjects[$i]) && property_exists($arrayOfObjects[$i], $key))
                return true;
        }

        return false;
    }

    /**
     * search for a value in an array of objects
     * @param array $arrayOfObjects
     * @param $value
     * @param null $key
     * @return bool
     */
    public function object_value_exists(array $arrayOfObjects, $value, $key = null) : bool
    {
        for ($i = 0; $i < count($arrayOfObjects); $i++)
        {
            if (is_object($arrayOfObjects[$i]))
                if ($key && $arrayOfObjects[$i]->{$key} == $value)
                    return true;
                else if (!$key)
                    foreach (get_object_vars($arrayOfObjects[$i]) as $prop => $val)
                        if ($val == $value)
                            return true;
        }

        return false;
    }

}
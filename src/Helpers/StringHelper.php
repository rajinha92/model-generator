<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 11:12
 */

namespace Rafael\ModelGenerator\Helpers;


class StringHelper
{


    /**
     * make name singular camel case
     * @param $name
     * @return string
     */
    public function make_class_name($name) : string
    {
        return ucfirst(str_singular(camel_case($name)));
    }


    /**
     * replace all $replacement keys by $replacement values in $content
     * @param $replacement
     * @param $content
     * @return mixed
     */
    public function replace_content($replacement, $content) : string
    {
        return str_replace(array_keys($replacement), array_values($replacement), $content);
    }

    /**
     * print $string to console and add line break
     * @param $string
     */
    public function console_log($string)
    {
        print $string . PHP_EOL;
    }

}
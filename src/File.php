<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 16:52
 */

namespace Rafael\ModelGenerator;


class File
{

    /**
     * @param $filename
     * @param $content
     * @return int
     */
    public function write($filename, $content) : int
    {
        if (!file_exists(dirname($filename)))
            mkdir(dirname($filename));

        $f      = fopen($filename, 'w');
        $result = fwrite($f, $content);
        fclose($f);

        return $result !== FALSE;
    }

}
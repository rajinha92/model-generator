<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 16:54
 */

namespace Rafael\ModelGenerator\Facades;


use Illuminate\Support\Facades\Facade;

class File extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Rafael\ModelGenerator\File::class;
    }
}
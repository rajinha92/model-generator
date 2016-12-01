<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 14:33
 */

namespace Rafael\ModelGenerator\Facades;


use Illuminate\Support\Facades\Facade;

class ArrayHelper extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \Rafael\ModelGenerator\Helpers\ArrayHelper::class;
    }

}
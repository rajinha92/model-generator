<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 16:35
 */

namespace Rafael\ModelGenerator;


use Illuminate\Support\ServiceProvider;
use Rafael\ModelGenerator\Commands\Generate;

class GeneratorServiceProvider extends ServiceProvider
{

    public function boot()
    {

        if ($this->app->runningInConsole())
        {
            $this->commands([
                Generate::class
            ]);
        }

    }

    public function register()
    {

    }

}
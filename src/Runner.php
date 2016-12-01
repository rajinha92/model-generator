<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 13:41
 */

namespace Rafael\ModelGenerator;

use Rafael\ModelGenerator\Contracts\Generator as ContractGenerator;

class Runner
{

    public function run(ContractGenerator $generator, $table_name, array $columns, $namespace = null, $options = [])
    {
        return $generator->make($table_name, $columns, $namespace, $options);
    }

}
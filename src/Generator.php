<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 29/11/16
 * Time: 15:08
 */

namespace Rafael\ModelGenerator;


use Rafael\ModelGenerator\Constants\Database as DatabaseConstants;
use Rafael\ModelGenerator\Facades\ArrayHelper;
use Rafael\ModelGenerator\Facades\StringHelper;
use Rafael\ModelGenerator\Generators\ForeignKeyMigrationGenerator;
use Rafael\ModelGenerator\Generators\MigrationGenerator;
use Rafael\ModelGenerator\Generators\ModelGenerator;

class Generator
{

    /**
     * @var Database
     */
    private $database;

    /**
     * Holds namespace option
     * @var string
     */
    private $namespace;

    /**
     * Holds array with options
     * @var array
     */
    private $options;

    /**
     * @var Runner
     */
    private $runner;

    /**
     * tables not to generate
     * @var array
     */
    private $safe_list = [
        'users',
        'migrations',
        'password_resets',
    ];

    /**
     * Generator constructor.
     * @param $namespace
     * @param array $options
     */
    public function __construct($namespace, array $options = [])
    {
        $this->database  = new Database();
        $this->runner    = new Runner();
        $this->namespace = $namespace;
        $this->options   = $options;
    }

    /**
     * Runs modelGenerator and migrationGenerator
     */
    public function run()
    {
        $tables = $this->database->getTables();
        foreach ($tables as $table)
        {
            if (!in_array(strtolower($table->TABLE_NAME), $this->safe_list))
            {
                $columns = $this->database->getColumns($table->TABLE_NAME);

                $foreigns = $this->database->getForeignKeys(
                    ArrayHelper::get_filtered_column($columns, 'COLUMN_NAME', function ($item)
                    {
                        return $item->COLUMN_KEY == DatabaseConstants::FOREIGN_KEY;
                    })
                );

                StringHelper::console_log("Generating model to $table->TABLE_NAME");
                if (!$this->runner->run(new ModelGenerator(null, $this->namespace), $table, $columns, []))
                    StringHelper::console_log("Falhou!");
                if (isset($this->options["migration"]) && $this->options["migration"])
                {
                    StringHelper::console_log("Generating migration to $table->TABLE_NAME");

                    $this->runner->run(new MigrationGenerator(), $table, $columns, []);

                    if (count($foreigns) > 0)
                        $this->runner->run(new ForeignKeyMigrationGenerator(), $table, $foreigns, []);
                }

            }
        }
    }

}
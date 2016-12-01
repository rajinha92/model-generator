<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 30/11/16
 * Time: 15:35
 */

namespace Rafael\ModelGenerator\Generators;


use Carbon\Carbon;
use Rafael\ModelGenerator\Contracts\Generator;
use Rafael\ModelGenerator\Facades\ArrayHelper;
use Rafael\ModelGenerator\Facades\StringHelper;

class ForeignKeyMigrationGenerator implements Generator
{
    /**
     * path of sample files
     * @var string
     */
    public $path_sample;

    /**
     * path to put generated models
     * @var string
     */
    public $path_migration;

    /**
     * ModelGenerator constructor.
     * @param null $path_sample
     * @param null $path_model
     */
    public function __construct($path_sample = null)
    {
        if (!$path_sample)
            $this->path_sample = config('model_generator.samples_folder', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Samples');
        $this->path_migration = database_path('migrations');
    }

    /**
     * Make file
     * @param $table_name
     * @param array $columns
     * @param array $options
     * @return bool
     */
    public function make($table_name, array $columns, array $options = []) : bool
    {
        $migration_name = 'foreign_keys_' . $table_name->TABLE_NAME;
        $replacement    = [
            '{migration_name}' => $migration_name,
            '{table_name}' => $table_name->TABLE_NAME,
            '{foreigners}' => '',
            '{fields}' => '',
        ];

        $replacement['{fields}'] = "'" . implode("','", ArrayHelper::get_filtered_column($columns, 'COLUMN_NAME', function () { return true; })) . "'";

        foreach ($columns as $column)
            $replacement['{foreigners}'] .= "\$table->foreign('$column->COLUMN_NAME')->references('$column->REFERENCED_COLUMN_NAME')->on('$column->REFERENCED_TABLE_NAME');";

        $migration_sample = file_get_contents(($this->path_sample ? $this->path_sample . DIRECTORY_SEPARATOR : '') . 'FKMigration.sample');
        $migration_sample = StringHelper::replace_content($replacement, $migration_sample);

        return file_put_contents(($this->path_migration ? $this->path_migration . DIRECTORY_SEPARATOR : '') . Carbon::now()->format('Y_m_d_His_') . $migration_name . '.php', $migration_sample);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 29/11/16
 * Time: 12:53
 */

namespace Rafael\ModelGenerator\Generators;


use Carbon\Carbon;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Rafael\ModelGenerator\Contracts\Generator;
use Rafael\ModelGenerator\Constants\Database as DatabaseConstants;
use Rafael\ModelGenerator\Facades\StringHelper;

class MigrationGenerator implements Generator
{

    use AppNamespaceDetectorTrait;

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
     * Make one or more files
     * @param $table_name
     * @param array $columns
     * @param array $options
     * @return mixed
     */
    public function make($table_name, array $columns, array $options = []) : bool
    {
        $migration_name = 'create_' . $table_name->TABLE_NAME . '_table';
        $replacement    = [
            '{migration_name}' => $migration_name,
            '{table_name}' => $table_name->TABLE_NAME,
            '{fields}' => ''
        ];

        foreach ($columns as $column)
        {
            $precision = "";

            switch ($column->DATA_TYPE)
            {
                case 'varchar':
                    $precision         = "," . $column->CHARACTER_MAXIMUM_LENGTH;
                    $column->DATA_TYPE = 'string';
                    break;
                case 'decimal':
                    $precision = "," . $column->NUMERIC_PRECISION . "," . $column->NUMERIC_SCALE;
                    break;
                case 'int':
                    $column->DATA_TYPE = 'integer';
                    break;
                case 'tinyint':
                    $column->DATA_TYPE = 'boolean';
                    break;
            }
            $type = $column->COLUMN_KEY == DatabaseConstants::PRIMARY_KEY ? (starts_with($column->DATA_TYPE, 'big') ? 'bigIncrements' : 'increments') : camel_case($column->DATA_TYPE);
            $replacement['{fields}'] .= ($replacement['{fields}'] != '' ? "\t\t\t" : '') . "\$table->$type('$column->COLUMN_NAME'$precision)" . ($column->IS_NULLABLE == 'YES' ? '->nullable()' : '') . ($column->COLUMN_DEFAULT ? '->default(' . $column->COLUMN_DEFAULT . ')' : '') . (ends_with($column->COLUMN_TYPE, 'unsigned') ? '->unsigned()' : '') . ($column->COLUMN_KEY == DatabaseConstants::UNIQUE_KEY ? '->unique()' : '') . ';' . PHP_EOL;
        }

        $migration_sample = file_get_contents(($this->path_sample ? $this->path_sample . DIRECTORY_SEPARATOR : '') . 'Migration.sample');
        $migration_sample = StringHelper::replace_content($replacement, $migration_sample);

        return file_put_contents(($this->path_migration ? $this->path_migration . DIRECTORY_SEPARATOR : '') . Carbon::createFromFormat('Y-m-d H:i:s', $table_name->CREATE_TIME)->format('Y_m_d_His_') . $migration_name . '.php', $migration_sample);
    }
}
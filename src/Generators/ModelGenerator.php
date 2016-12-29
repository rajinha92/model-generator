<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 29/11/16
 * Time: 10:55
 */

namespace Rafael\ModelGenerator\Generators;


use Illuminate\Console\AppNamespaceDetectorTrait;
use Rafael\ModelGenerator\Contracts\Generator;
use Rafael\ModelGenerator\Facades\File;
use Rafael\ModelGenerator\Facades\StringHelper;
use Rafael\ModelGenerator\Constants\Database as DatabaseConstants;
use Rafael\ModelGenerator\Facades\ArrayHelper;

class ModelGenerator implements Generator
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
    public $path_model;

    /**
     * namespace
     * @var string
     */
    public $namespace;

    /**
     * root namespace
     * @var string
     */
    public $app_name;

    /**
     * ModelGenerator constructor.
     * @param null $path_sample
     * @param null $namespace
     */
    public function __construct($path_sample = null, $namespace = null)
    {
        $this->app_name = str_replace('\\', '', $this->getAppNamespace());

        if (!$path_sample)
            $this->path_sample = config('model_generator.samples_folder', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Samples');

        $this->path_model = app_path($namespace);
        $this->namespace  = $namespace;
    }

    /**
     * Make Model file
     * @param $table_name
     * @param array $columns
     * @param array $foreigns
     * @param array $options
     * @return bool
     */
    public function make($table_name, array $columns, array $foreigns = [], array $options = []) : bool
    {
        $model_name  = StringHelper::make_class_name($table_name->TABLE_NAME);
        $replacement = [
            '{app_name}' => $this->app_name,
            '{namespace}' => $this->namespace ? '\\' . $this->namespace : '',
            '{model_name}' => $model_name,
            '{table_name}' => StringHelper::make_table_name($table_name->TABLE_NAME),
            '{primary_key}' => 'id', //TODO: get by parameter
            '{timestamps}' => '',
            '{softDeleteImport}' => '',
            '{softDelete}' => '',
            '{fillable}' => '',
            '{dates}' => '',
            '{relations}' => '',
        ];

        if (!ArrayHelper::object_value_exists($columns, 'created_at') && !ArrayHelper::object_value_exists($columns, 'updated_at'))
            $replacement['{timestamps}'] = 'false';
        else
            $replacement['{timestamps}'] = 'true';

        if (ArrayHelper::object_value_exists($columns, 'deleted_at'))
        {
            $replacement["{softDeleteImport}"] = 'use Illuminate\Database\Eloquent\SoftDeletes;';
            $replacement["{softDelete}"]       = 'use SoftDeletes;';
        }

        $replacement['{fillable}'] = "'" . implode(
                "'," . PHP_EOL . "\t\t'",
                ArrayHelper::get_filtered_column($columns, 'COLUMN_NAME', function ($item)
                {
                    return $item->COLUMN_KEY != DatabaseConstants::PRIMARY_KEY;
                })
            ) . "'";

        $replacement['{dates}'] = "'" . implode(
                "','",
                ArrayHelper::get_filtered_column($columns, 'COLUMN_NAME', function ($item)
                {
                    return in_array($item->DATA_TYPE, ['date', 'timestamp', 'datetime']);
                })
            ) . "'";

        $replacement['{relations}'] = '';

        foreach ($foreigns as $foreign)
        {
            $belongsTo = file_get_contents(($this->path_sample ? $this->path_sample . DIRECTORY_SEPARATOR : '') . 'BelongsTo.sample');
            $replacement['{relations}'] .= StringHelper::replace_content([
                '{referenced_name}' => $foreign->REFERENCED_TABLE_NAME,
                '{referenced_model_name}' => StringHelper::make_class_name($foreign->REFERENCED_TABLE_NAME),
                '{column_name}' => $foreign->COLUMN_NAME
            ], $belongsTo);
        }

        $model_sample = file_get_contents(($this->path_sample ? $this->path_sample . DIRECTORY_SEPARATOR : '') . 'Model.sample');
        $model_sample = StringHelper::replace_content($replacement, $model_sample);

        return (bool)File::write(($this->path_model ? $this->path_model . DIRECTORY_SEPARATOR : '') . $model_name . '.php', $model_sample);

    }
}

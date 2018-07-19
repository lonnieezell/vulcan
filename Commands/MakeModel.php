<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Model,
 * and optionally pull the fields from
 * an existing database table.
 *
 * @package Vulcan\Commands
 */
class MakeModel extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'Vulcan';

    protected $name = 'make:model';

    protected $description = 'Creates a skeleton Model file, optionally from a database.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'make:model [model_name] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'model_name' => 'The model file name'
    ];

     /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = array(
        '-n' => 'Set model namespace',
        '-f' => 'overwrite files'
    );   

    protected $optionsList = [
        'name'            => '',
        'table'           => '',
        'primaryKey'      => '',
        'dateFormat'      => 'datetime',
        'useSoftDeletes'  => true,
        'allowedFields'   => '',
        'useTimestamps'   => true,
        'createdField'    => 'created_at',
        'updatedField'    => 'updated_at',
        'returnType'      => 'array',
        'validationRules' => '[]',
    ];

    /**
     * Creates a skeleton controller file.
     */
    public function run(array $params = [])
    {
        /*
         * Model name
         */
        $name = array_shift($params);

        if (empty($name))
        {
            $name = CLI::prompt('Model name');
        }

        $this->optionsList['name'] = ucfirst($name);

        $this->collectOptions($this->optionsList['name'], CLI::getOptions());

        $data = $this->prepareData();

        $overwrite = (bool)CLI::getOption('f');

        $destination = $this->determineOutputPath('Models', $this->optionsList['namespace']).$name.'.php';

        try
        {
            $this->copyTemplate('Model/Model', $destination, $data, $overwrite);
        } catch (\Exception $e)
        {
            $this->showError($e);
        }
    }

    //--------------------------------------------------------------------

    protected function collectOptions(string $name, array $options = [])
    {
        helper('inflector');

        // Table name
        if (empty($this->optionsList['table']))
        {
            $this->optionsList['table'] = empty($options['table'])
                ? CLI::prompt('Table name', plural(strtolower(str_replace('Model', '', $name))))
                : $this->optionsList['table'];
        }

        // Primary Key
        if (empty($this->optionsList['primaryKey']))
        {
            $this->optionsList['primaryKey'] = empty($options['primaryKey'])
                ? CLI::prompt('Primary key', 'id')
                : $this->optionsList['primaryKey'];
        }

        $this->optionsList['namespace'] = CLI::getOption('n') ?? 'App';

        // Collect the fields from the table itself, if we have one
        $this->optionsList['allowedFields'] = $this->tableInfo($this->optionsList['table'], $options);
    }

    /**
     * Grabs the fields from the CLI options and gets them ready for
     * use within the views.
     */
    protected function parseFieldString(array $fields)
    {
        if (empty($fields))
        {
            return null;
        }

        $fields = explode(',', $fields);

        $new_fields = [];

        foreach ($fields as $field)
        {
            $pop = [null, null, null];
            list($field, $type, $size) = array_merge(explode(':', $field), $pop);
            $type = strtolower($type);

            // Strings
            if (in_array($type, ['char', 'varchar', 'string']))
            {
                $new_fields[] = [
                    'name' => $field,
                    'type' => 'text',
                ];
            } // Textarea
            else if ($type == 'text')
            {
                $new_fields[] = [
                    'name' => $field,
                    'type' => 'textarea',
                ];
            } // Number
            else if (in_array($type, ['tinyint', 'int', 'bigint', 'mediumint', 'float', 'double', 'number']))
            {
                $new_fields[] = [
                    'name' => $field,
                    'type' => 'number',
                ];
            } // Date
            else if (in_array($type, ['date', 'datetime', 'time']))
            {
                $new_fields[] = [
                    'name' => $field,
                    'type' => $type,
                ];
            }
        }

        // Convert to objects
        array_walk($new_fields, function (&$item, $key)
        {
            $item = (object)$item;
        });

        return $new_fields;
    }

    //--------------------------------------------------------------------

    /**
     * Get the structure and details for the fields in the specified DB table.
     *
     * @param string $table
     * @param array  $options
     *
     * @return bool
     */
    protected function tableInfo(string $table, array $options = [])
    {
        try
        {
            $db = \Config\Database::connect();
            $db->initialize();
        }
        catch (\Throwable $e)
        {
            // If an error was thrown here, it's likely
            // because we can't connect to the database.
            // So - let the user know and move on.
            CLI::error($e->getMessage());
            return false;
        }

        if (! $db->tableExists($this->optionsList['table']))
        {
            if (empty($options['fields']))
            {
                return false;
            }

            // Nothing in the db, then grab from the cli
            $fields = $this->parseFieldString($options['fields']);
        } else
        {
            $fields = $db->getFieldData($table);
        }

        if (empty($fields))
        {
            return false;
        }

        $this->optionsList['useTimestamps']  = false;
        $this->optionsList['useSoftDeletes'] = false;

        // Still here? Try to determine correct values from the database
        // for things like primary key, etc.
        foreach ($fields as $field)
        {
            // Primary key?
            if (! empty($field->primary_key) && $field->primary_key == 1)
            {
                $this->optionsList['primaryKey'] = $field->name;
            } // Timestamps
            elseif ($field->name == $this->optionsList['createdField'])
            {
                $this->optionsList['useTimestamps'] = true;
            } // Soft Deletes
            elseif ($field->name == 'deleted')
            {
                $this->optionsList['useSoftDeletes'] = true;
            }
        }

        // Set our validation rules based on these fields.
        $this->optionsList['validationRules'] = $this->buildValidationRules($fields);

        return $fields;
    }

    //--------------------------------------------------------------------

    /**
     * Takes the information from getFieldData() and creates the basic
     * validation rules for those fields.
     *
     * @param array $fields
     *
     * @return mixed|string
     */
    protected function buildValidationRules(array $fields)
    {
        if (empty($fields))
        {
            return;
        }

        $rules = [];

        foreach ($fields as $field)
        {
            if (in_array($field->name, ['created_at', 'updated_at']))
            {
                continue;
            }

            $rule = [];

            switch ($field->type)
            {
                // Numeric Types
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'integer':
                case 'bigint':
                    $rule[] = 'integer';
                    break;
                case 'decimal':
                case 'dec':
                case 'numeric':
                case 'fixed':
                    $rule[] = 'decimal';
                    break;
                case 'float':
                case 'double':
                    $rule[] = 'numeric';
                    break;

                // Date types don't have many defaults we can go off of...

                // Text Types
                case 'char':
                case 'varchar':
                case 'text':
                    $rule[] = 'alpha_numeric_spaces';
                    break;
            }

            if (! empty($field->max_length))
            {
                $rule[] = "max_length[{$field->max_length}]";
            }

            $rules[$field->name] = implode('|', $rule);
        }

        $str = $this->stringify($rules);

        // Clean up the resulting array a bit
        $str = substr_replace($str, "\n]", -3);

        return $str;
    }

    //--------------------------------------------------------------------

    /**
     * Converts the data into string that can be inserted in the model.
     */
    public function prepareData()
    {
        $data = [
            'name'            => $this->optionsList['name'],
            'table'           => $this->optionsList['table'],
            'primaryKey'      => $this->optionsList['primaryKey'],
            'useSoftDeletes'  => $this->optionsList['useSoftDeletes'] === true ? 'true' : 'false',
            'useTimestamps'   => $this->optionsList['useTimestamps'] === true ? 'true' : 'false',
            'createdField'    => $this->optionsList['createdField'],
            'updatedField'    => $this->optionsList['updatedField'],
            'returnType'      => $this->optionsList['returnType'],
            'validationRules' => $this->optionsList['validationRules'],
            'dateFormat'      => $this->optionsList['dateFormat'],
            'today'           => date('Y-m-d H:ia'),
        ];

        if (is_array($this->optionsList['allowedFields']))
        {
            $fields = [];

            foreach ($this->optionsList['allowedFields'] as $field)
            {
                if ($field->name == $data['primaryKey']
                    || $field->name == $data['createdField']
                    || $field->name == $data['updatedField'])
                {
                    continue;
                }

                $fields[] = "'".$field->name."'";
            }
        }

        $data['allowedFields'] = isset($fields)
            ? implode(', ', $fields)
            : null;

        return $data;
    }

}

<?php 
namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Entity based off of a db table.
 *
 * @package Vulcan\Commands
 */
class MakeEntity extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'Vulcan';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'make:entity';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Creates a skeleton Entity class, optionally from a database table.';

    protected $options = [
        'table'         => null,
	    'propertyList'  => ''
    ];

    /**
     * Creates a skeleton command file.
     */
    public function run(array $params=[])
    {
        /*
         * Name 
         */
        $name = array_shift($params);

        if (empty($name))
        {
            $name = CLI::prompt('Entity name');
        }

	    // Format to CI standards
	    $name = ucfirst($name);
	    $view = 'Entity/Entity';

	    $this->collectProperties($name);

	    $data = [
			'namespace'     => 'Codeigniter\Entities',
			'name'          => $name,
			'today'         => date('Y-m-d H:i:a'),
			'propertyList'  => $this->options['propertyList']
        ];

        $destination = $this->determineOutputPath('Entities').$name.'.php';

        $overwrite = (bool)CLI::getOption('f');

        try {
            $this->copyTemplate($view, $destination, $data, $overwrite);
        }
        catch (\Exception $e)
        {
            $this->showError($e);
        }
    }

	protected function collectProperties(string $name)
	{
		helper('inflector');

		// Table name
		if (empty($this->options['table']))
		{
			$this->options['table'] = empty($options['table'])
				? CLI::prompt('Table name', plural(strtolower(str_replace('Model', '', $name))))
				: $options['table'];
		}

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

		$fields = null;

		if (! $db->tableExists($this->options['table']))
		{
			if (empty($options['fields']))
			{
				return false;
			}
		} else
		{
			$fields = $db->getFieldData($this->options['table']);
		}

		if (empty($fields))
		{
			return false;
		}

		$this->options['propertyList'] = $this->formatProperties($fields);
    }

	protected function formatProperties(array $fields)
	{
		$list = [];

		foreach ($fields as $field)
		{
			$list[] = "\tprotected \${$field->name};";
		}

		return implode("\n", $list);
    }
}

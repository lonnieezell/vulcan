<?php
namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Filter.
 *
 * @package Vulcan\Commands
 */
class MakeFilter extends BaseCommand
{
	use GeneratorTrait;

	protected $group = 'Vulcan';

	/**
	 * The Command's name
	 *
	 * @var string
	 */
	protected $name = 'make:filter';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Creates a skeleton Filter class.';

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
			$name = CLI::prompt('Filter name');
		}

		// Format to CI standards
		$name = ucfirst($name);
		$view = 'Filter/Filter';

		$data = [
			'namespace'     => 'App\Filters',
			'name'          => $name,
			'today'         => date('Y-m-d H:i:a'),
		];

		$destination = $this->determineOutputPath('Filters').$name.'.php';

		$overwrite = (bool)CLI::getOption('f');

		try {
			$this->copyTemplate($view, $destination, $data, $overwrite);
		}
		catch (\Exception $e)
		{
			$this->showError($e);
		}
	}

}

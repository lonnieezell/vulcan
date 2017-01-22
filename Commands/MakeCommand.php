<?php 
namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Command
 *
 * @package Vulcan\Commands
 */
class MakeCommand extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'Vulcan';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'make:command';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Creates a skeleton command file.';

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
            $name = CLI::prompt('Command name');
        }

        // Format to CI standards
        $name = ucfirst($name);
        $view = 'Command/Command';

        $data = [
          'namespace' => 'Codeigniter\Commands',
          'name'      => $name,
          'today'     => date('Y-m-d H:i:a')
        ];

        $destination = $this->determineOutputPath('Commands').$name.'.php';

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
<?php 
namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Migration
 *
 * @package Vulcan\Commands
 */
class MakeMigration extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'Vulcan';

    /**
     * The Migration's name
     *
     * @var string
     */
    protected $name = 'make:migration';

    /**
     * the Migration's short description
     *
     * @var string
     */
    protected $description = 'Creates a skeleton migration file.';

    /**
     * Creates a skeleton Migration file.
     */
    public function run(array $params=[])
    {
        /*
         * Name 
         */
        $name = array_shift($params);

        if (empty($name))
        {
            $name = CLI::prompt('Migration name');
        }

        // Format to CI standards
        $name = ucfirst($name);
        $view = 'Migration/Migration';

        $data = [
          'namespace' => is_null(CLI::getOption('n')) ? 'App' : CLI::getOption('n'),
          'name'      => $name,
          'today'     => date('Y-m-d H:i:a')
        ];

        $destination = $this->determineOutputPath('Database\Migrations',$data['namespace']).$name.'.php';

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
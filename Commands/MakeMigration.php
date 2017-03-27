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
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'make:migration [migration_name] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'migration_name' => 'The migration file name'
    ];

     /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '-n' => 'Set migration namespace',
        '-f' => 'overwrite files'
    ];    

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
          'namespace' => 'namespace' => CLI::getOption('n') ?? 'App',
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
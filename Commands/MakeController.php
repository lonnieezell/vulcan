<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Vulcan\Libraries\GeneratorTrait;

/**
 * Creates a skeleton Controller
 *
 * @package Vulcan\Commands
 */
class MakeController extends BaseCommand
{
    use GeneratorTrait;

    protected $group = 'Vulcan';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Creates a skeleton Controller file.';

    /**
     * Creates a skeleton controller file.
     */
    public function run(array $params=[])
    {
        /*
         * Controller name
         */
        $name = array_shift($params);

        if (empty($name))
        {
            $name = CLI::prompt('Controller name');
        }

        // Format to CI standards
        $name = ucfirst($name);

        /*
         * Generate CRUD methods?
         */
        $crud = CLI::prompt('Generate CRUD?', ['y', 'n']);
        $view = $crud == 'y'
            ? 'Controller/SimpleController'
            : 'Controller/CRUDController';

        $data = [
            'namespace'      => 'App\Controllers',
            'controllerName' => $name,
            'today'          => date('Y-m-d H:i:a')
        ];

        $destination = $this->determineOutputPath('Controllers').$name.'.php';

        try {
            $this->copyTemplate($view, $destination, $data);
        }
        catch (\Exception $e)
        {
            $this->showError($e);
        }
    }
}

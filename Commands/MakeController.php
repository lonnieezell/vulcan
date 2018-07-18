<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\BaseCommand;
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
     *
     * @param array $params
     */

      /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'make:controller [controller_name] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'controller_name' => 'The controller file name'
    ];

     /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '-n' => 'Set Controller namespace',
        '-f' => 'overwrite files'
    ];
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
            ? 'Controller/CRUDController'
            : 'Controller/SimpleController';

        $data = [
            'namespace' => CLI::getOption('n') ?? 'App',
            'name'      => $name,
            'today'     => date('Y-m-d H:i:a')
        ];

        // Collect additional information for CRUDControllers.
        if ($crud == 'y')
        {
            $data = array_merge($data, $this->getCRUDOptions());
        }

        $destination = $this->determineOutputPath('Controllers',$data['namespace']).$name.'.php';

        $overwrite = (bool)CLI::getOption('f');

        try {
            $this->copyTemplate($view, $destination, $data, $overwrite);
        }
        catch (\Exception $e)
        {
            $this->showError($e);
        }
    }

    //--------------------------------------------------------------------

    public function getCRUDOptions()
    {
        /*
         * Model
         */
        $model = CLI::getOption('model');

        if (empty($model))
        {
            $model = CLI::prompt('Model name');
        }

        /*
         * Views?
         */
        $views = (bool)CLI::getOption('withViews');

        if (empty($views))
        {
            $views = CLI::prompt('Generate views', ['y', 'n']);
            $views = $views =='y'
                ? true
                : false;
        }

        return [
            'model' => $model ?? 'UnnamedModel',
            'views' => $views
        ];
    }

}

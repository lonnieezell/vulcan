<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;

/**
 * Creates a skeleton Controller
 *
 * @package Vulcan\Commands
 */
class MakeController extends BaseCommand
{
    protected $group = 'Vulcan';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'console';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Interact with your application.';

    /**
     * Creates a skeleton controller file.
     */
    public function run(array $params=[])
    {
    }
}

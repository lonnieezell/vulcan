<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Psy\Configuration;
use Psy\Shell;

/**
 * Creates a skeleton Controller
 *
 * @package Vulcan\Commands
 */
class Console extends BaseCommand
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
     * Runs the Psysh Shell.
     */
    public function run(array $params = [])
    {
      $usageException = null;

      $color   = (boolean) CLI::getOption('color');
      $noColor = (boolean) CLI::getOption('no-color');
      $config  = array();

      // Handle --config
      if ($configFile = CLI::getOption('config')) {
          $config['configFile'] = $configFile;
      }

      // Handle --color and --no-color
      if ($color && $noColor) {
          $usageException = new \RuntimeException('Using both "--color" and "--no-color" options is invalid.');
      } elseif ($color) {
          $config['colorMode'] = Configuration::COLOR_MODE_FORCED;
      } elseif ($noColor) {
          $config['colorMode'] = Configuration::COLOR_MODE_DISABLED;
      }

      // Psysh Shell instance
      $shell = new Shell(new Configuration($config));

      // Handle --help
      if ($usageException !== null || (boolean) CLI::getOption('help')) {
          if ($usageException !== null) {
              echo $usageException->getMessage() . PHP_EOL;
          }
          $this->printHelpScreen($shell->getVersion(), 'console');

          exit($usageException === null ? 0 : 1);
      }

      // Handle --version
      if ((boolean) CLI::getOption('version')) {
          echo $shell->getVersion() . PHP_EOL;
          exit(0);
      }

      // Pass additional arguments to Shell as 'includes'
      // $shell->setIncludes($input->getArgument('include'));

      // Pass additional variables to Shell
      // Example:
        // 'controller' => $this # will include this controller 
      // $shell->setScopeVariables([]);

      try
      {
        $shell->run();
      }
      catch (Exception $e)
      {
        echo $e->getMessage() . PHP_EOL;

        // TODO: this triggers the "exited unexpectedly" logic in the
        // ForkingLoop, so we can't exit(1) after starting the shell...
        // we need to fix this :)
        // exit(1);
      }
    }

    protected function printHelpScreen($version, $name)
    {
      // TODO: Figure out how to print a help screen without this ugly echo
      echo <<<EOL
$version

Usage:
$name [--version] [--help] [files...]

Options:
--help     -h Display this help message.
--config   -c Use an alternate PsySH config file location.
--cwd         Use an alternate working directory.
--version  -v Display the PsySH version.
--color       Force colors in output.
--no-color    Disable colors in output.

EOL;
    }
}

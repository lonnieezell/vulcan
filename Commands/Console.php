<?php namespace Vulcan\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use Psy\VersionUpdater\Checker;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Psy\Configuration;
use Psy\Shell;

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
      $usageException = null;

      // TODO: Use the Codeigniter ArgvInput instead
      $input = new ArgvInput();

      try {
          $input->bind(new InputDefinition([
              new InputOption('help',     'h',  InputOption::VALUE_NONE),
              new InputOption('config',   'c',  InputOption::VALUE_REQUIRED),
              new InputOption('version',  'v',  InputOption::VALUE_NONE),
              new InputOption('cwd',      null, InputOption::VALUE_REQUIRED),
              new InputOption('color',    null, InputOption::VALUE_NONE),
              new InputOption('no-color', null, InputOption::VALUE_NONE),

              new InputArgument('include', InputArgument::IS_ARRAY),
          ]));
      } catch (\RuntimeException $e) {
          $usageException = $e;
      }

      $config = array();

      // Handle --config
      if ($configFile = $input->getOption('config')) {
          $config['configFile'] = $configFile;
      }

      // Handle --color and --no-color
      if ($input->getOption('color') && $input->getOption('no-color')) {
          $usageException = new \RuntimeException('Using both "--color" and "--no-color" options is invalid.');
      } elseif ($input->getOption('color')) {
          $config['colorMode'] = Configuration::COLOR_MODE_FORCED;
      } elseif ($input->getOption('no-color')) {
          $config['colorMode'] = Configuration::COLOR_MODE_DISABLED;
      }

      $shell = new Shell(new Configuration($config));

      // Handle --help
      if ($usageException !== null || $input->getOption('help')) {
          if ($usageException !== null) {
              echo $usageException->getMessage() . PHP_EOL . PHP_EOL;
          }

          $version = $shell->getVersion();
          $name    = basename(reset($_SERVER['argv']));
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
          exit($usageException === null ? 0 : 1);
      }

      // Handle --version
      if ($input->getOption('version')) {
          echo $shell->getVersion() . PHP_EOL;
          exit(0);
      }

      // Pass additional arguments to Shell as 'includes'
      // $shell->setIncludes($input->getArgument('include'));

      try {
          // And go!
          $shell->run();
      } catch (Exception $e) {
          echo $e->getMessage() . PHP_EOL;

          // TODO: this triggers the "exited unexpectedly" logic in the
          // ForkingLoop, so we can't exit(1) after starting the shell...
          // we need to fix this :)
          // exit(1);
      }
    }
}

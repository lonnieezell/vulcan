<?php namespace Vulcan\Libraries;

use CodeIgniter\View\Parser;
use Config\Services;
use CodeIgniter\CLI\CLI;
use Config\Autoload;
use Vulcan\Libraries\FileKit;

/**
 * Class GeneratorTrait
 *
 * @package Vulcan\Libraries
 */
trait GeneratorTrait
{
    /**
     * @var \CodeIgniter\View\Parser
     */
    protected $parser;

     /**
     * @var const
     */
    protected $rootPath = BASEPATH . "../";

    //--------------------------------------------------------------------

    /**
     * Creates a file at the specified path with the given contents.
     *
     * @param      $path
     * @param null $contents
     *
     * @return bool
     */
    public function createFile($path, $contents = null, $overwrite = false, $perms = 0644)
    {
        $path = $this->sandbox($path);

        $file_exists = is_file($path);

        // Does file already exist?
        if ($file_exists)
        {
            if (! $overwrite)
            {
                CLI::write(CLI::color("\t".strtolower(lang('Vulcan.exists')).": ", 'blue').str_replace(realpath($this->rootPath), '',
                        $path));

                return true;
            }

            unlink($path);
        }

        // Do we need to create the directory?
        $segments = explode('/', $path);
        array_pop($segments);
        $folder = implode('/', $segments);

        if (! is_dir($folder))
        {
            $this->createDirectory($folder);
        }

        helper('filesystem');

        if (! write_file($path, $contents))
        {
            throw new \RuntimeException(sprintf(lang('Vulcan.errorWritingFile'), $path));
        }

        chmod($path, $perms);

        if ($overwrite && $file_exists)
        {
            CLI::write(CLI::color("\t".strtolower(lang('Vulcan.overwrote'))." ", 'light_red').str_replace(realpath($this->rootPath), '',
                    $path));
        } else
        {
            CLI::write(CLI::color("\t".strtolower(lang('Vulcan.created'))." ", 'yellow').str_replace(realpath($this->rootPath), '',
                    $path));
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Creates a new directory at the specified path.
     *
     * @param            $path
     * @param int|string $perms
     *
     * @return bool
     */
    public function createDirectory($path, $perms = 0755)
    {
        $path = $this->sandbox($path);

        if (is_dir($path))
        {
            return $this;
        }

        if (! mkdir($path, $perms, true))
        {
            throw new \RuntimeException(sprintf(lang('Vulcan.errorCreatingDir'), $path));
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Copies a file from the current template group to the destination.
     *
     * @param      $source
     * @param      $destination
     * @param bool $overwrite
     *
     * @return bool
     */
    public function copyFile($source, $destination, $overwrite = false)
    {
        $source = $this->sandbox($source);

        if (! file_exists($source))
        {
            return null;
        }

        $content = file_get_contents($source);

        return $this->createFile($destination, $content, $overwrite);
    }

    //--------------------------------------------------------------------

    /**
     * Attempts to locate a template within the current template group,
     * parses it with the passed in data, and writes to the new location.
     *
     * @param       $template
     * @param       $destination
     * @param array $data
     * @param bool  $overwrite
     *
     * @return $this
     */
    public function copyTemplate($template, $destination, $data = [], $overwrite = false)
    {
        if (! is_array($data))
        {
            $data = [$data];
        }

        $content = $this->render($template, $data);

        return $this->createFile($destination, $content, $overwrite);
    }

    //--------------------------------------------------------------------


    /**
     * Injects a block of code into an existing file. Using options
     * you can specify where the code should be inserted. Available options
     * are:
     *      prepend         - Place at beginning of file
     *      append          - Place at end of file
     *      before  => ''   - Insert just prior to this line of text (don't forget the line ending "\n")
     *      after   => ''   - Insert just after this line of text (don't forget the line ending "\n")
     *      replace => ''   - a simple string to be replaced. All locations will be replaced.
     *      regex   => ''   - a pregex pattern to use to replace all locations.
     *
     * @param              $path
     * @param              $content
     * @param array|string $options
     *
     * @return $this
     */
    public function injectIntoFile($path, $content, $options = 'append')
    {
        $kit = new FileKit();

        if (is_string($options))
        {
            $action = $options;
        } else if (is_array($options) && count($options))
        {
            $keys   = array_keys($options);
            $action = array_shift($keys);
            $param  = $options[$action];
        }

        switch (strtolower($action))
        {
            case 'prepend':
                $success = $kit->prepend($path, $content);
                break;
            case 'before':
                $success = $kit->before($path, $param, $content);
                break;
            case 'after':
                $success = $kit->after($path, $param, $content);
                break;
            case 'replace':
                $success = $kit->replaceIn($path, $param, $content);
                break;
            case 'regex':
                $success = $kit->replaceWithRegex($path, $param, $content);
                break;
            case 'append':
            default:
                $success = $kit->append($path, $content);
                break;
        }

        if ($success)
        {
            CLI::write(CLI::color("\t".strtolower(lang('Vulcan.modified'))." ", 'cyan').str_replace(realpath($this->rootPath), '',
                    $path));
        } else
        {
            CLI::write(CLI::color("\t".strtolower(lang('Vulcan.error'))." ", 'light_red').str_replace(realpath($this->rootPath), '',
                    $path));
        }

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Adds a new route to the application's route file.
     *
     * @param        $left
     * @param        $right
     *
     * @param array  $options
     * @param string $method
     *
     * @return $this
     */
    public function route($left, $right, $options = [], $method = 'any')
    {
        $option_str = '[';

        foreach ($options as $key => $value)
        {
            $option_str .= "";
        }

        $option_str .= ']';

        $content = "\$routes->{$method}('{$left}', '{$right}', {$option_str});\n";

        return $this->injectIntoFile(APPPATH.'Config/Routes.php', $content,
            ['after' => "// Auto-generated routes go here\n"]);
    }

    //--------------------------------------------------------------------

    /**
     * Renders a single generator template. The file must be in a folder
     * under the template group named the same as $this->generator_name.
     * The file must have a '.tpl.php' file extension.
     *
     * @param       $template_name
     * @param array $data
     *
     * @return string The rendered template
     */
    public function render($template_name, $data = [], $folder = null)
    {
        if (empty($this->parser))
        {
            $path         = realpath(__DIR__.'/../Views/').'/';
            $this->parser = new Parser(new \Config\View(), $path);
        }

        if (is_null($this->parser))
        {
            throw new \RuntimeException('Unable to create Parser instance.');
        }

        $output = $this->parser
            ->setData($data)
            ->render($template_name);

        // To allow for including any PHP code in the templates,
        // replace any '@php' and '@=' tags with their correct PHP syntax.
        $output = str_replace('@php', '<?php', $output);
        $output = str_replace('@=', '<?=', $output);

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Forces a path to exist within the current application's folder.
     * This means it must be in APPPATH,  or FCPATH. If it's not
     * the path will be forced within the APPPATH, possibly creating a
     * ugly set of folders, but keeping the user from accidentally running
     * an evil generator that might have done bad things to their system.
     *
     * @todo Look into how to support third-party namespaces.
     *
     * @param $path
     *
     * @return string
     */
    public function sandbox($path)
    {
        $path = $this->normalizePath($path);

        // If it's writing to BASEPATH - FIX IT
        if (strpos($path, $this->normalizePath(BASEPATH)) === 0)
        {
            return APPPATH.$path;
        }

        // Exact match for FCPATH?
        if (strpos($path, $this->normalizePath(FCPATH)) === 0)
        {
            return $path;
        }

        // Exact match for APPPATH?
        if (strpos($path, $this->normalizePath(APPPATH)) === 0)
        {
            return $path;
        }

        return $path;
    }

    //--------------------------------------------------------------------

    /**
     * Converts an array to a string representation.
     *
     * @param $array
     *
     * @return string
     */
    protected function stringify($array, $depth=0)
    {
        if (! is_array($array))
        {
            return '';
        }

        $str = '';

        if ($depth > 1)
        {
            $str .= str_repeat("\t", $depth);
        }

        $depth++;

        $str .= "[\n";

        foreach ($array as $key => $value)
        {
            $str .= str_repeat("\t", $depth +1);

            if (! is_numeric($key))
            {
                $str .= "'{$key}' => ";
            }

            if (is_array($value))
            {
                $str .= $this->stringify($value, $depth);
            }
            else if (is_bool($value))
            {
                $b = $value === true ? 'true' : 'false';
                $str .= "{$b},\n";
            }
            else if (is_numeric($value))
            {
                $str .= "{$value},\n";
            }
            else
            {
                $str .= "'{$value}',\n";
            }
        }

        $str .= str_repeat("\t", $depth) ."],";

        return $str;
    }

    //--------------------------------------------------------------------

    /**
     * Normalizes a path and cleans it up for healthy use within
     * realpath() and helps to mitigate changes between Windows and *nix
     * operating systems.
     *
     * Found at http://php.net/manual/en/function.realpath.php#112367
     *
     * @param $path
     *
     * @return string
     */
    protected function normalizePath($path)
    {
        // Array to build a new path from the good parts
        $parts = [];

        // Replace backslashes with forward slashes
        $path = str_replace('\\', '/', $path);

        // Combine multiple slashes into a single slash
        $path = preg_replace('/\/+/', '/', $path);

        // Collect path segments
        $segments = explode('/', $path);

        // Initialize testing variable
        $test = '';

        foreach ($segments as $segment)
        {
            if ($segment != '.')
            {
                $test = array_pop($parts);

                if (is_null($test))
                {
                    $parts[] = $segment;
                } else if ($segment == '..')
                {
                    if ($test == '..')
                    {
                        $parts[] = $test;
                    }

                    if ($test == '..' || $test == '')
                    {
                        $parts[] = $segment;
                    }
                } else
                {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }

        return implode('/', $parts);
    }

    //--------------------------------------------------------------------

    /**
     * Creates the destination path for a file.
     *
     * @todo Automatically handle namespaces/subfolders and putting them in the right location.
     *
     * @param string $folder
     *
     * @return string
     */
    protected function determineOutputPath($folder='', $namespace = 'App')
    {
        // Get namespace location form  PSR4 paths.
        $config = new Autoload();      
        $location = $config->psr4[$namespace];

        $path = $location . "/". $folder; 

        return rtrim($path, '/ ') .'/';
    }

    //--------------------------------------------------------------------
}

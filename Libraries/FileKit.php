<?php namespace Vulcan\Libraries;
/**
 * Vulcan
 *
 * A set of code-generation commands for CodeIgniter 4.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vulcan
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2017, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 */

/**
 * Class FileKit
 *
 * Provides some utility commands for working with files.
 */
class FileKit {

    /**
     * Appends data to the end of a file.
     *
     * @param $file
     * @param $content
     * @return bool|int
     */
    public function append($file, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $fh = fopen($file, 'a');
        $result = fwrite($fh, $content);
        fclose($fh);

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * Prepends string content to a file. For very large files
     * this method could have memory issues, but the primary usage
     * of source files shouldn't ever get large enough to cause issues.
     *
     * @param $file
     * @param $content
     * @return bool|int
     */
    public function prepend($file, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException( sprintf(lang('Vulcan.errorReadingFile'), $file));
        }

        $result = file_put_contents($file, $content . $file_contents);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Inserts $content before the line that matches $before. NOT case-
     * sensitive.
     *
     * @param $file
     * @param $before
     * @param $content
     * @return int
     */
    public function before($file, $before, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $lines = file($file);

        if ($lines === false)
        {
            throw new \RuntimeException( sprintf( lang('Vulcan.errorFileNotFound'), $file ));
        }

        // Where to insert the row.
        $location = null;

        foreach ($lines as $index => $line)
        {
            if (strtolower($line) == strtolower($before) )
            {
                $location = $index;
                break;
            }
        }

        array_splice($lines, $location, 0, $content);

        $result = file_put_contents($file, $lines);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    public function after($file, $after, $content)
    {
        if (empty($content))
        {
            return true;
        }

        // Ensure that $content has a newline at the end
        $content = rtrim($content) ."\n";

        $lines = file($file);

        if ($lines === false)
        {
            throw new \RuntimeException( sprintf( lang('Vulcan.errorFileNotFound'), $file ) );
        }

        // Where to insert the row.
        $location = null;

        foreach ($lines as $index => $line)
        {
            if (strtolower($line) == strtolower($after) )
            {
                $location = $index;
                break;
            }
        }

        array_splice($lines, $location +1, 0, $content);

        $result = file_put_contents($file, $lines);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Replaces all instances of $search in the file with $replace.
     *
     * @param $file
     * @param $search
     * @param $replace
     * @return int
     */
    public function replaceIn($file, $search, $replace)
    {
        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException( sprintf( lang('Vulcan.errorReadingFile'), $file ) );
        }

        $file_contents = str_replace($search, $replace, $file_contents);

        $result = file_put_contents($file, $file_contents);

        return (bool)$result;
    }

    //--------------------------------------------------------------------

    /**
     * Uses preg_replace to replace content within the file.
     *
     * @param $file
     * @param $pattern
     * @param $replace
     * @return int
     */
    public function replaceWithRegex($file, $pattern, $replace)
    {
        $file_contents = file_get_contents($file);

        if ($file_contents === false)
        {
            throw new \RuntimeException( sprintf( lang('Vulcan.errorReadingFile'), $file ) );
        }

        $file_contents = preg_replace($pattern, $replace, $file_contents);

        $result = false;

        // Don't let us erase a file!
        if (! empty($file_contents))
        {
            $result = file_put_contents( $file, $file_contents );
        }

        return (bool)$result;
    }

    //--------------------------------------------------------------------

}

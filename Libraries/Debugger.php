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
  * Class Debugger
  *
  * Provides some utility commands for debuggin scripts.
  */
 class Debugger
 {
   /**
    * When a script reaches an specific point, execution will be suspended and
    * it will be dropped into a PsySH shell. Then the program state is loaded
    * and available for you to inspect and experiment with.
    *
    * @return string
    */
   public static function check() : string
   {
     return 'extract(\Psy\Shell::debug(get_defined_vars(), isset($this) ? $this : null));';
   }
 }

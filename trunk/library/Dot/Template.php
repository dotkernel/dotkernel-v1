<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Template engine, based on PHPLIB library
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/


class Dot_Template
{
	/**
		* Class name - default = 'Template'
		* @access private
		* @var string
		*/
	private $className = 'Template';
	/**
		* If set, echo assignments
		* @access private
		* @var bool
		*/
	private $debug = false;
	/**
		* If set, echo blocks time parse
		* @access private
		* @var bool
		*/
	private $debugBlock = false;
	/**
		* Relative filenames are relative to this pathname
		* @access private
		* @var string
		*/
	private $root = '..';
	/**
		* $file[handle] = 'filename';
		* @access private
		* @var array
		*/
	private $file = array();
	/**
		* fallback paths that should be defined in a child class
		* @access private
		* @var array
		*/
	private $fileFallbacks = array();
	/**
		* $varkeys[key] = 'key'
		* @access private
		* @var array
		*/
	private $varkeys = array();
	/**
		* $varvals[key] = 'value';
		* @access private
		* @var array
		*/
	private $varvals = array();
	/**
		* 'remove'  => remove undefined variables
		* 'comment' => replace undefined variables with comments
		* 'keep'    => keep undefined variables
		* @access private
		* @var string
		*/
	private $unknowns = 'remove';
	/**
		* 'yes' => halt,
		* 'report' => report error, continue,
		* 'no' => ignore error quietly
		* @access private
		* @var string
		*/
	private $haltOnError = 'yes';
	/**
		* The last error message is retained here
		* @access private
		* @var string
		* @see halt
		*/
	private $lastError = '';
	/**
		* Determines whether Template outputs filename comments.
		* false = no filename outputs
		* true = HTML comments (e.g. <!-- START FILE $filename -->) placed in output
		* @access private
		* @var string
		*/
	private $filenameComments = false;
	/**
		* Determines the regular expression used to find unknown variable tags.
		* 'loose'  = traditional match all curly braces with no whitespace between
		* 'strict' = adopts PHP's variable naming rules
		* @access private
		* @var bool
		*/
	private $unknownRegexp = 'loose';
	/**
		* Start time 
		* @access private
		* @var array
		*/
	private $start_time = array();
	/**
		* End time 
		* @access private
		* @var array
		*/
	private $endTime = array();
	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Template
	 */
	protected static $_instance = null;	
	/**
	 * Singleton pattern implementation makes 'new' unavailable
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return void
	 */
	protected function __construct($root = '.', $unknowns = 'remove', $fallback='')
	{
		$this->setRoot($root);
		$this->setUnknowns($unknowns);
		if (is_array($fallback)) $this->fileFallbacks = $fallback;
	}
	/**
	 * Singleton pattern implementation makes 'clone' unavailable
	 * @access protected
	 * @return void
	 */
	protected function __clone()
	{}
	/**
	 * Returns an instance of Dot_View
	 * Singleton pattern implementation
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) {
			self::$_instance = new self($root, $unknowns, $fallback);			
		}
		return self::$_instance;
	}	
	/**
	 * Checks that $root is a valid directory and if so sets this directory as the
	 * base directory from which templates are loaded by storing the value in
	 * $this->root. Relative filenames are prepended with the path in $this->root.
	 * @access public
	 * @return bool
	 */
	public function setRoot($root)
	{
		if(preg_match('-/$-', $root))
		{
			$root = substr($root, 0, -1);
		}
		if (!is_dir($root))
		{
			$this->halt("setRoot: $root is not a directory.");
			return false;
		}
		$this->root = $root;
		return true;
	}
	/**
	 * Start the time to measure something
	 * @access private
	 * @return array
	 */
	private function startTimer()
	{
			$mtime = microtime ();
			$mtime = explode (' ', $mtime);
			$mtime = $mtime[1] + $mtime[0];
			return $mtime;
	}
	/**
	 * Return the end time
	 * @access private
	 * @return float
	 */
	private function endTimer($varname)
	{
			$mtime = microtime ();
			$mtime = explode (' ', $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$endtime = $mtime;
			$totaltime = round(($endtime - $this->start_time[$varname]),2);
			return $totaltime;
	}
	/**
	 * Sets the policy for dealing with unresolved variable names.
	 * @access public
	 * @param string $unknowns - default = 'remove'
	 * @return void
	 */
	public function setUnknowns($unknowns = 'remove')
	{
		$this->unknowns = $unknowns;
	}
	/**
	 * Inspired From PEAR HTML_Template_PHPLIB 1.4.0
	 * Checks if the given variable exists.
	 * When an array is given, it is checked if all variables exist.
	 * @access public 
	 * @param string|array $var Variable to check
	 * @return bool
	 */
	public function exists($var)
	{
			if (is_array($var)) {
					$isset = true;
					foreach ($var as $varname) {
							$isset = $isset & isset($this->varVals[$varname]);
					}
					return $isset > 0;
			} else {
					return isset($this->varVals[$var]);
			}
	}
	/**
	 * Defines a filename for the initial value of a variable.
	 * It may be passed either a varname and a file name as two strings or
	 * a hash of strings with the key being the varname and the value
	 * being the file name.
	 * The new mappings are stored in the array $this->file.
	 * The files are not loaded yet, but only when needed.
	 * USAGE: setFile(array $filelist = (string $varname => string $filename))
	 * or
	 * USAGE: setFile(string $varname, string $filename)
	 * @access public
	 * @param string $varname
	 * @param string $filename
	 * @return bool
	 */
	public function setFile($varname, $filename = '')
	{
		if (!is_array($varname))
		{
			if ($filename == '')
			{
				$this->halt('setFile: For varname '.$varname .'filename is empty.');
				return false;
			}
			$this->file[$varname] = $this->filename($filename);
			if ($this->file[$varname] === false) {
					return false;
			}
		}
		else
		{
			reset($varname);
			while(list($v, $f) = each($varname))
			{
				if ($f == '')
				{
					$this->halt('setFile: For varname '.$v.' filename is empty.');
					return false;
				}
				$this->file[$v] = $this->filename($f);
			}
		}
		return true;
	}
	/**
	 * A variable $parent may contain a variable block defined by:
	 * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;. 
	 * This public function removes that block from $parent and replaces it 
	 * with a variable reference named $name.
	 * The block is inserted into the varkeys and varvals hashes. If $name is
	 * omitted, it is assumed to be the same as $varname.
	 * Blocks may be nested but care must be taken to extract the blocks in order
	 * from the innermost block to the outermost block.
	 * USAGE: setBlock(string $parent, string $varname, [string $name = ''])
	 * @access public
	 * @param string $parent
	 * @param string $varname
	 * @param string $name
	 * @return bool
	 */
	public function setBlock($parent, $varname, $name = '')
	{
		$this->start_time[$varname] = 0;
		$this->start_time[$varname] = $this->startTimer();
		if (!$this->loadFile($parent))
		{
			$this->halt('setBlock: unable to load '.$parent);
			return false;
		}
		if ($name == '')
		{
			$name = $varname;
		}

		$str = $this->getVar($parent);
		$reg = '/[ \t]*<!--\s+BEGIN '.$varname.'\s+-->\s*?\n?(\s*.*?\n?)'
             . '\s*<!--\s+END '.$varname.'\s+-->\s*?\n?/sm';
		preg_match_all($reg, $str, $m);
		if (!isset($m[1][0]))
		{
			$this->halt('setBlock: unable to set block '.$varname);
			return false;
		}
		$str = preg_replace($reg, '{' . $name . '}', $str);
		if (isset($m[1][0])) $this->setVar($varname, $m[1][0]);
		$this->setVar($parent, $str);
		return true;
	}
	/**
	 * Sets the value of a variable.
	 * It may be called with either a varname and a value as two strings or an
	 * an associative array with the key being the varname and the value being
	 * the new variable value.
	 * The public function inserts the new value of the variable into the $varkeys and
	 * $varvals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this public function.
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing variable instead of replacing it. The default is to replace.
	 * This feature was introduced after the 7.2d release.
	 * USAGE: setVar(string $varname, [string $value = ''], [boolean $append = false])
	 * or
	 * USAGE: setVar(array $varname = (string $varname => string $value), 
	 * [mixed $dummy_var], [boolean $append = false])
	 * @access public
	 * @param mixed $varname
	 * @param mixed $value
	 * @param bool $append
	 * @return bool
	 */
	public function setVar($varname, $value = '', $append = false)
	{
		if (!is_array($varname))
		{
			if (!empty($varname))
			{
				if ($this->debug & 1)
				{
					printf("<b>setVar:</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($value));
				}
				$this->varkeys[$varname] = '/'.$this->varname($varname).'/';
				if ($append && isset($this->varvals[$varname]))
				{
					$this->varvals[$varname] .= $value;
				}
				else 	$this->varvals[$varname] = $value;
			}
		}
		else
		{
			reset($varname);
			while(list($k, $v) = each($varname))
			{
				if (!empty($k))
				{
					if ($this->debug & 1)
					{
						printf("<b>setVar:</b> (with array) <b>%s</b> = '%s'<br>\n", $k, htmlentities($v));
					}
					$this->varkeys[$k] = '/'.$this->varname($k).'/';
					if ($append && isset($this->varvals[$k]))
					{
						$this->varvals[$k] .= $v;
					}
					else 	$this->varvals[$k] = $v;
				}
			}
		}
	}
	/** 
	 * Unsets a variable completely.
	 * It may be called with either a varname as a string or an array with the
	 * values being the varnames to be cleared.
	 * The public function removes the variable from the $varkeys and $varvals hashes.
	 * It is not necessary for a variable to exist in these hashes before calling
	 * this public function.
	 * USAGE: unsetVar(string $varname)
	 * or
	 * USAGE: unsetVar(array $varname = (string $varname))
	 * @access public
	 * @param mixed $varname
	 * @return void
	 */
	public function unsetVar($varname)
	{
		if (!is_array($varname))
		{
			if (!empty($varname))
			{
				if ($this->debug & 1)
				{
					printf("<b>unsetVar:</b> (with scalar) <b>%s</b><br>\n", $varname);
				}
				unset($this->varkeys[$varname]);
				unset($this->varvals[$varname]);
			}
		}
		else
		{
			reset($varname);
			while(list($k, $v) = each($varname))
			{
				if (!empty($v))
				{
					if ($this->debug & 1)
					{
						printf("<b>unsetVar:</b> (with array) <b>%s</b><br>\n", $v);
					}
					unset($this->varkeys[$v]);
					unset($this->varvals[$v]);
				}
			}
		}
	}
	/**
	 * Fills in all the variables contained within the variable named
	 * $varname. The resulting value is returned as the public function result and the
	 * original value of the variable varname is not changed. The resulting string
	 * is not 'finished'.
	 * RETURNS: the value of the variable $varname with all variables substituted or FALSE if halted
	 * USAGE: subst(string $varname)
	 * @access public
	 * @param mixed $varname
	 * @return mixed
	 */
	public function subst($varname)
	{
		$varvals_quoted = array();
		if (!$this->loadFile($varname))
		{
			$this->halt('subst: unable to load '.$varname);
			return false;
		}
		// quote the replacement strings to prevent bogus stripping of special chars
		reset($this->varvals);
		while(list($k, $v) = each($this->varvals))
		{
			$varvals_quoted[$k] = preg_replace(array('/\\\\/', '/\$/'), array('\\\\\\\\', '\\\\$'), $v);
		}
		$str = $this->getVar($varname);
		$str = preg_replace($this->varkeys, $varvals_quoted, $str);
		return $str;
	}
	/**
	 * This is shorthand for print $this->subst($varname). See subst for further	 details.
	 * USAGE: psubst(string $varname)
	 * @access public
	 * @param string $varname
	 * @return bool
	 */
	public function psubst($varname)
	{
		print $this->subst($varname);
		return false;
	}
	/**
	 * Substitutes the values of all defined variables in the variable
	 * named $varname and stores or appends the result in the variable named $target.
	 * It may be called with either a target and a varname as two strings or a
	 * target as a string and an array of variable names in varname.
	 * The public function inserts the new value of the variable into the $varkeys and
	 * $varvals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this public function.
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing target variable instead of replacing it. The default is to
	 * replace.
	 * If $target and $varname are both strings, the substituted value of the
	 * variable $varname is inserted into or appended to $target.
	 * If $handle is an array of variable names the variables named by $handle are
	 * sequentially substituted and the result of each substitution step is
	 * inserted into or appended to in $target. The resulting substitution is
	 * available in the variable named by $target, as is each intermediate step
	 * for the next $varname in sequence. Note that while it is possible, it
	 * is only rarely desirable to call this public function with an array of varnames
	 * and with $append = true.
	 * USAGE: parse(string $target, string $varname, [boolean $append])
	 * or
	 * USAGE: parse(string $target, array $varname = (string $varname), [boolean $append])
	 * @access public
	 * @param string $target
	 * @param mixed $varname
	 * @param bool $append
	 * @return mixed
	 */
	public function parse($target, $varname, $append = false)
	{
		if (!is_array($varname))
		{
			$str = $this->subst($varname);
			if ($append)
			{
				$this->setVar($target, $this->getVar($target) . $str);
			}
			else  $this->setVar($target, $str);
		}
		else
		{
			reset($varname);
			while(list($i, $v) = each($varname))
			{
				$str = $this->subst($v);
				if ($append)
				{
					$this->setVar($target, $this->getVar($target) . $str);
				}
				else  $this->setVar($target, $str);
			}
		}
		if(isset($this->start_time[$varname]) && $varname===strtolower($varname))
		{
			$this->endTime[$varname] =$this->endTimer($varname);
		}
		return $this->getVar($target);
	}
	/**
	 * This is shorthand for print $this->parse(...) and is public functionally identical.
	 * USAGE: pparse(string $target, string $varname, [boolean $append])
	 * or
	 * USAGE: pparse(string $target, array $varname = (string $varname), [boolean $append])
	 * @access public
	 * @param string $target
	 * @param mixed $varname
	 * @param bool $append
	 * @return bool FALSE
	 */
	public function pparse($target, $varname, $append = false)
	{
		if($target=='OUTPUT' && $this->debugBlock==true)
		{
			$totalTimeBlock = 0;
			foreach($this->start_time as $ky => $val)
			{
				$totalTimeBlock +=$this->endTime[$ky];
				printf("<b>block:</b>  <b>%s</b> = '%f'<br>\n", $ky,$this->endTime[$ky]);
			}
			printf("<b>Total time for blocks:</b>  '%f'<br>\n", $totalTimeBlock);
		}
		print $this->finish($this->parse($target, $varname, $append));
		return false;
	}
	/**
	 * Returns an associative array of all defined variables with the
	 * name as the key and the value of the variable as the value.
	 * This is mostly useful for debugging. Also note that $this->debug can be used
	 * to echo all variable assignments as they occur and to trace execution.
	 * USAGE: getVars()
	 * @access public
	 * @return array 
	 */
	public function getVars()
	{
		reset($this->varkeys);
		while(list($k, $v) = each($this->varkeys))
		{
			$result[$k] = $this->getVar($k);
		}
		return $result;
	}
	/**
	 * Returns the value of the variable named by $varname.
	 * If $varname references a file and that file has not been loaded yet, the
	 * variable will be reported as empty.
	 * When called with an array of variable names this public function will return a a
	 * hash of variable values keyed by their names.
	 * USAGE: getVar(string $varname)
	 * or
	 * USAGE: getVar(array $varname)
	 * @access public
	 * @param mixed $varname
	 * @return mixed
	 */
	public function getVar($varname)
	{
		if (!is_array($varname))
		{
			$str = (isset($this->varvals[$varname]))?  $this->varvals[$varname]: '';
			if ($this->debug & 2)
			{
				printf ("<b>getVar</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($str));
			}
			return $str;
		}
		else
		{
			reset($varname);
			while(list($k, $v) = each($varname))
			{
				$str = (isset($this->varvals[$v])) ?  $this->varvals[$v]: '';
				if ($this->debug & 2)
				{
					printf ("<b>getVar:</b> (with array) <b>%s</b> = '%s'<br>\n", $v, htmlentities($str));
				}
				$result[$v] = $str;
			}
			return $result;
		}
	}
	/**
	 * Returns a hash of unresolved variable names in $varname, keyed
	 * by their names (that is, the hash has the form $a[$name] = $name).
	 * USAGE: getUndefined(string $varname)
	 * @access public
	 * @param string $varname
	 * @return array or false
	 */
	public function getUndefined($varname)
	{
		if (!$this->loadFile($varname))
		{
			$this->halt('getUndefined: unable to load '.$varname);
			return false;
		}
		preg_match_all(
		(('loose' == $this->unknownRegexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"),
		$this->getVar($varname),
		$m);
		$m = $m[1];
		if (!is_array($m))  return false;

		reset($m);
		while(list($k, $v) = each($m))
		{
			if (!isset($this->varkeys[$v]))
			{
				$result[$v] = $v;
			}
		}
		if (isset($result) && count($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	/**
	 * Returns the finished version of $str.
	 * USAGE: finish(string $str)
	 * @access public
	 * @param string $str
	 * @return string
	 */
	public function finish($str)
	{
		switch ($this->unknowns)
		{
			case 'keep':
			break;

			case 'remove':
				$str = preg_replace(
				(('loose' == $this->unknownRegexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"),
				"",
				$str);
			break;

			case 'comment':
				$str = preg_replace(
					 (('loose' == $this->unknownRegexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"),
					"<!-- Template variable \\1 undefined -->",
					$str);
			break;
		}
		return $str;
	}
	/**
	 * Prints the finished version of the value of the variable named by $varname.
	 * USAGE: p(string $varname)
	 * @access public
	 * @param string $varname
	 * @return void
	 */
	public function p($varname)
	{
		print $this->finish($this->getVar($varname));
	}
	/**
	 * Returns the finished version of the value of the variable named  by $varname.
	 * USAGE: get(string $varname)
	 * @access public
	 * @param string $varname
	 * @return void
	 */
	public function get($varname)
	{
		return $this->finish($this->getVar($varname));
	}
	/**
	 * When called with a relative pathname, this function will return the pathname
	 * with $this->root prepended. Absolute pathnames are returned unchanged.
	 * RETURNS: a string containing an absolute pathname.
	 * USAGE: filename(string $filename)
	 * @access private
	 * @param string $filename
	 * @return string
	 * @see set_root
	 */
	private function filename($filename)
	{
		if (substr($filename, 0, 1) != '/'
				&& substr($filename, 0, 1) != '\\'
				&& substr($filename, 1, 2) != ':\\'
				&& substr($filename, 1, 2) != ':/'
				)
		{
			$filename = $this->root.'/'.$filename;
		}
		if (!file_exists($filename))
		{
			$this->halt('filename: file '.$filename.' does not exist.');
		}
		if (is_array($this->fileFallbacks) && count($this->fileFallbacks) > 0)
			{
				reset($this->fileFallbacks);
				while (list(,$v) = each($this->fileFallbacks))
					{
						if (file_exists($v.basename($filename)))
							{
								return $v.basename($filename);
							}
					}
				$this->halt(sprintf('filename: file %s does not exist in the fallback paths %s.',$filename, implode(',', $this->fileFallbacks)));
				return false;
			}
		/*else
			{
				$this->halt(sprintf('filename: file %s does not exist.', $filename));
				return false;
			}*/
		return $filename;
	}
	/**
	 * Will construct a regexp for a given variable name with any  special
	 * chars quoted.
	 * @access private
	 * @param string $varname
	 * @return string
	 */
	private function varname($varname)
	{
		return preg_quote('{'.$varname.'}');
	}
	/**
	 * If a variable's value is undefined and the variable has a filename stored in
	 * $this->file[$varname] then the backing file will be loaded and the file's
	 * contents will be assigned as the variable's value.
	 * USAGE: loadFile(string $varname)
	 * @access private
	 * @param string $varname
	 * @return bool 
	 */
	private function loadFile($varname)
	{
		if (!isset($this->file[$varname]))
		{
			// $varname does not reference a file so return
			return true;
		}
		if (isset($this->varvals[$varname]))
		{
			// will only be unset if varname was created with setFile and has never been loaded
			// $varname has already been loaded so return
			return true;
		}
		$filename = $this->file[$varname];
		//  use @file here to avoid leaking filesystem information if there is an error
		$str = implode('', @file($filename));
		if (empty($str))
		{
			$this->halt('loadFile: While loading $varname, '.$filename.' does not exist or is empty.');
			return false;
		}
		if ($this->filenameComments)
		{
			$str = "<!-- START FILE ".$filename." -->\n$str<!-- END FILE ".$filename." -->\n";
		}
		$this->setVar($varname, $str);
		return true;
	}
	/**
	 * Is called whenever an error occurs and will handle the error according
	 * to the  policy defined in $this->haltOnError. Additionally the	error message will be saved
	 * in $this->lastError.
	 * USAGE: halt(string $msg)
	 * @access private
	 * @param string $msg
	 * @return bool 
	 */
	private function halt($msg)
	{
		$this->lastError = $msg;
		if ($this->haltOnError != 'no')
		{
			$this->haltMsg($msg);
		}
		if ($this->haltOnError == 'yes')
		{
			die('<b>Halted.</b>');
		}
		return false;
	}
	/**
	 * Prints an error message.
	 * It can be overridden by your subclass of Template. It will be called with an
	 * error message to display.
	 * USAGE: haltMsg(string $msg)
	 * @access private
	 * @param string $msg
	 * @return void 
	 */
	private function haltMsg($msg)
	{
		$the_error ='';
		$the_error .= "\n\n ".$msg."\n\n";
		$the_error .= "Date: ".date("l dS of F Y h:i:s A");

		$out = "<html><head><title>Template Error</title>
				<style>P,BODY{ font-family: trebuchet MS,sans-serif; font-size:11px;
					}</style></head><body>&nbsp;<br><br><blockquote><b>There is an error with the
					template system.</b><br><b>Error Returned: </b><br>
			<form name='mysql'><textarea rows=\"5\" cols=\"60\">".htmlspecialchars($the_error)."</textarea></form></blockquote></body></html>";
		if(APPLICATION_ENV != 'production') print $out;
		exit();
	}
	/**
	 * Returns the last error message if any
	 * @access public
	 * @return boolean|string Last error message if any
	 */
	public function getLastError()
	{
		if ($this->lastError == '')
		{
			return false;
		}
		return $this->lastError;
	}
	/**
	 * Initialize the value of a variable.
	 * It may be called with either a varname and a value as two strings or an
	 * an associative array with the key being the varname and the value being
	 * the new variable value.
	 * The public function inserts the new value of the variable into the $varkeys and
	 * $varvals hashes. It is not necessary for a variable to exist in these hashes
	 * before calling this public function.
	 * An optional third parameter allows the value for each varname to be appended
	 * to the existing variable instead of replacing it. The default is to replace.
	 * USAGE: initVar(string $varname, [string $value = ''], [boolean $append = false])
	 * or
	 * USAGE: initVar(array $varname = (string $varname => string $value),
	 * [mixed $dummy_var], [boolean $append = false])
	 * or
	 * USAGE: initVar(array $varname = (string $value), 
	 * [mixed $dummy_var], [boolean $append = false])
	 * @access public
	 * @param mixed $varname
	 * @param mixed $value
	 * @param bool $append
	 * @return void
	 */
	public function initVar($varname, $value = '', $append = false)
	{
		if (!is_array($varname))
		{
			if (!empty($varname))
			{
				$this->setVar($varname, $value, $append);
			}
		}
		else
		{
			reset($varname);
			while(list($k, $v) = each($varname))
			{
				if(is_int($k))
				{					
					$this->setVar($v, $value, $append);
				}
				elseif(is_string($k))
				{
					$this->setVar($k, $v, $append);
				}
				elseif ($this->debug & 1)
				{
					printf("<b>initVar:</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($value));
				}
			}
		}
	}
	/**
	 * Initialize a block
	 * A variable $parent may contain a variable block defined by:
	 * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;. 
	 * This public function removes that block from $parent and replaces it 
	 * with a variable reference named $name.
	 * The block is inserted into the varkeys and varvals hashes. If $name is
	 * omitted, it is assumed to be the same as $varname.
	 * Blocks may be nested but care must be taken to extract the blocks in order
	 * from the innermost block to the outermost block.
	 * USAGE: setBlock(string $parent, string $varname, [string $name = ''])
	 * @access public
	 * @param mixed $target
	 * @param mixed $value
	 * @param string $name
	 * @return bool
	 */
	public function initBlock($target, $value = '', $append = false)
	{
		if(!is_array($target))
		{
			$this->parse($target, $value, $append);
		}
		
		else
		{			
			reset($target);			
			while(list($k, $v) = each($target))
			{
				if(is_int($k))
				{				
					$this->parse($v, $value, $append);
				}
				elseif(is_string($k))
				{
					$this->parse($k, $v, $append);
				}
				else  $this->setVar($target, $str);
			}
		}
	}
}
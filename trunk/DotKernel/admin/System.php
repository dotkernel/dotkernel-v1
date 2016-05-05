<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @copyright  Copyright (c) 2009-2016 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* System Model
* Here are all the actions related to the system settings
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/
class System extends Dot_Model
{
	/**
	 * Ini Values
	 * @var array
	 */
	private $_ini;
	
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get MySQL Version
	 * @access public
	 * @return string
	 */
	public function getMysqlVersion()
	{
		$select = $this->db->select()
							->from('', array('ve' => new Zend_Db_Expr('version()')));
		return $this->db->fetchOne($select);
	}
	
	/**
	 * Get settings that are by default editable
	 * @access public
	 * @param int $isEditable [optional]
	 * @return array
	 */
	public function getSettings($isEditable='1')
	{
		$select = $this->db->select()
							->from('setting')
							->where('isEditable = ? ', $isEditable);
		return $this->db->fetchAll($select);
	}
	
	/**
	 * Update settings
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function updateSettings($data)
	{
		foreach ($data as $k => $v)
		{
			$this->db->update('setting', array('value' => $v), $this->db->quoteIdentifier('key').' = '.$this->db->quote($k));
		}
	}
	
	/**
	 * Get GeoIp Version release
	 * Return an array with keys "country" & "city"
	 * @access public
	 * @return array
	 */
	public function getGeoIpVersion()
	{
		$return = array('country' => '-', 'city' => '-', 'local' => '-');
		
		// let's see the version of local .dat file 
		$geoipPath = $this->config->resources->geoip->path;
		$geoipVersion = explode(" ", Dot_Geoip_Country::geoipDatabaseInfo($geoipPath));
		$return['local'] = $geoipVersion[0] . ' ' . Dot_Kernel::TimeFormat($geoipVersion[1]);
	
		// do we have geoIP server-wide ? 
		if(function_exists('geoip_database_info'))
		{
			if(geoip_db_avail(GEOIP_COUNTRY_EDITION))
			{
				$info = explode(" ",geoip_database_info(GEOIP_COUNTRY_EDITION));
				$return['country']  = $info[0].' '.Dot_Kernel::TimeFormat($info[1]);
			}
		}

		return $return;
	}
	
	/**
	 * Get Hostname
	 * Return a string
	 * @access public
	 * @return string
	 */
	public static function getSystemHostname()
	{
		if(version_compare(PHP_VERSION, '5.3.0', '>='))
		{
			$hostName = gethostname();
		}
		else
		{
			$hostName = php_uname('n');
		}
		return $hostName;
	}
	
	/**
	 * Get information about APC
	 * Returns an array with the following elements:
	 *    name: string with the name of the extension: either old APC or new APCu
	 *    version: a string with the version of APC that is currently installed or an empty string if it's not installed
	 *    enabled: boolean
	 * @access public
	 * @return array
	 */
	public function getAPCInfo()
	{
		$result = array();
		// check APC . First for APCu, then if is not present, check for old APC
		$apcu= phpversion('apcu');
		if($apcu)
		{
			$apcVersion = $apcu;
			$result["name"] = 'APCu';
		}
		else
		{
			$apcVersion = phpversion('apc');
			$result["name"] = 'APC';
		}		
		$result["version"] = ($apcVersion===false) ? "" : $apcVersion;
		$result["enabled"] = (function_exists('apc_cache_info') && (@apc_cache_info() !== false));
		return $result;
	}
	
	/**
	 * Checks session-related configuration
	 * @access private
	 * @return array - empty if no errors were found
	 */
	private function _getSessionNotifications()
	{
		$errors = $warnings = $infos= array();
		$messages = array(); 
		
		$config = Zend_Registry::get('configuration');
		$namespacePrefix = 'default';
		if(isset($config->session->namespace_prefix) && is_string($config->session->namespace_prefix))
		{
			$namespacePrefix = $config->session->namespace_prefix;
		}
		
		$messageKey = 'session namespace prefix'; 
		if($namespacePrefix == 'default')
		{
			$messages[$messageKey][] = 'Session prefix is not defined in application.ini ';
			$messages[$messageKey][] = 'This might lead to undesired results when working with multiple DotKernel instances on the same server';
			$messages[$messageKey][] = 'Please add <b>resources.session.namespace_prefix = "your_session_prefix"</b> in the application.ini file';
			$messages[$messageKey]['namespacePrefix'] = 'Current namespace prefix is: ' . $namespacePrefix;
		}
		
		if($namespacePrefix == 'dotkernel')
		{
			$messages[$messageKey][] = 'Session prefix was not changed from its default ';
			$messages[$messageKey][] = 'This might lead to undesired results when working with multiple DotKernel instances on the same server';
			$messages[$messageKey][] = 'Please change <b>resources.session.namespace_prefix = "dot_kernel"</b> to <b>resources.session.namespace_prefix = "your_session_prefix"</b> in the application.ini file';
			$messages[$messageKey]['namespacePrefix'] = 'Current namespace prefix is: ' . $namespacePrefix;
		}
		
		if(!isset($config->resources->session->save_path))
		{
			$warnings['session save_path'][] = 'Session prefix was not defined or changed from its default';
			$warnings['session save_path'][] = 'This might lead to undesired results when working with multiple DotKernel instances on the same server';
			$errors = $messages;
		}

		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	
	/**
	 * Get Cache Notifications
	 * @access private
	 * @return array
	 */
	private function _getCacheNotifications()
	{
		$errors = $warnings = $infos= array();
		
		$cacheSimpleTest = Dot_Cache::testCache();
		$cacheTagsTest = Dot_Cache::testTags();

		$cacheInfo = Dot_Cache::getCacheInfo();
		if($cacheInfo['config']['namespace'] == 'dotkernel' || $cacheInfo['config']['namespace'] == 'default')
		{
			$errors['Cache Namespace Warning'][] = 'Please change the cache namespace';
			$errors['Cache Namespace Warning'][] = 'Cache might not work correctly if the namespace is dotkernel';
			$errors['Cache Namespace Warning'][] = 'You can change it from the cache.namespace setting in application.ini';
		}

		if($cacheSimpleTest == true)
		{

			foreach($cacheInfo['config'] as $key => $value)
			{
				$infos['Cache Info'][] = $key . ' : ' . $value;
			}
			if($cacheTagsTest == true )
			{
				$infos['Cache Info'][] = 'tags are supported';
			}
			else
			{
				$warnings['Cache Test Failed'][] = 'Cache does not support tags';
				$warnings['Cache Test Failed'][] = 'Check cache provider in application.ini';
				$warnings['Cache Test Failed'][] = ''.
				'More info: <a href="http://framework.zend.com/manual/1.12/en/zend.cache.backends.html" target="_blank"> ZF Cache Backends </a>';
			}
		}
		else
		{
			$errors['Cache Test Failed'][] = 'Cache is not working or disabled';
			$errors['Cache Test Failed'][] = 'Check cache settings or if cache module is supported';
			$errors['Cache Test Failed'][] = ''.
									'More info: <a href="http://www.dotkernel.com/dotkernel/caching-in-dotkernel-using-zend-framework/"  target="_blank"> Caching in DotKernel</a>';
		}
		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	
	/**
	 * Get File-related Notifications
	 * 
	 * Checks file permissions and files that should be deleted
	 * This will return "Make Writable", "Make Unwritable" & "Delete Files" warnings
	 *  
	 * @access private
	 * @return array
	 */
	private function _getFileNotifications()
	{
		$errors = $warnings = $infos= array();
		
		$errors['Make Writable'] = array();
		$errors['Make Unwritable'] = array(); 
		
		//ignore permission warning if OS is Windows
		if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		{
			// warning if application.ini is writable
			if(is_writable(APPLICATION_PATH . "/configs/application.ini"))
			{
				$errors["Make Unwritable"][] = 'configs/application.ini';
			}
		
			// only the folders set in application.ini (folders.permission[]) should be writable
			$folderException = $this->config->folders->permission->toArray();
			// go through all folders in the tree
			$folders = $this->_listDirectory(APPLICATION_PATH);
			foreach($folders as $path)
			{
				// exceptions are configured in application.ini. they should be writable
				$isException = false;
				foreach($folderException as $exception)
				{
					if(strpos($path, $exception) !== false)
					{
						$isException = true;
						break;
					}
				}
				if($isException)
				{
					if(! is_writable($path) && $path === $exception)
					{
						$errors["Make Writable"][] = $path;
					}
				}
				else
				{
					if(is_writable($path))
					{
		
						$errors["Make Unwritable"][] = $path;
					}
				}
			}
			// info about how to add exception
			if(count($errors["Make Unwritable"]))
			{
				$errors["Make Unwritable"][] = '**  <em>It is possible to add your writable folders to the exclude list by adding it
										as folders.permission[] exception in application.ini</em>';
			}
		}
		
		// check for files that should be deleted
		$filesToDelete = array("dot_kernel.sql", "readme.txt", "dk.php");
		foreach($filesToDelete as $file)
		{
			if(file_exists(APPLICATION_PATH . "/" . $file))
			{
				$errors['Delete Files'][] = $file;
			}
		}
		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	
	private function _getPluginNotifications()
	{
		$errors = $warnings = $infos= array();
		
		// plugin check
		$pluginHandler = Plugin_Loader::getInstance();
		$pluginData = $pluginHandler->getAllPlugins();
		foreach($pluginData as $plugin)
		{
			// check if the class is missing
			if( ! $pluginHandler->pluginExists($plugin['vendor'], $plugin['pluginName']))
			{
				$errors['Plugin Check'][] = 'Plugin '. $plugin['pluginName'] . ' (by ' .$plugin['vendor']. ') is missing';
			}
			// check if the plugin is enabled
			elseif( ! $plugin['enabled'])
			{
				$warnings['Plugin Check'][] = 'Plugin '. $plugin['pluginName'] . ' (by ' .$plugin['vendor']. ') is not enabled';
			}
		}
		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	
	/**
	 * Checks the admin credentials and if his email or devEmails contain team@dotkernel.com
	 * 
	 * Checks if the devEmails setting and admin account contain team@dotkernel.com
	 * Also checks if the password for "admin" is "dot"
	 * 
	 * @access private
	 * @return array
	 */
	private function _getAdminNotifications()
	{
		$errors = $warnings = $infos= array();
		
		// check that the default admin user isn't enabled
		$dotAuth = Dot_Auth::getInstance();
		$defaultAdminValid = $dotAuth->process('admin', array("username"=>"admin", "password"=>"dot"), $storeInSession = false);
		if ($defaultAdminValid)
		{
			$errors["Security Warning"][] = "Please change the password of the oldest admin user or deactivate him";
		}
		
		// if the oldest admin have the same email team@dotkernel.com
		$select = $this->db->select()->from('admin', 'email')->where('isActive = ?', '1')->order('dateCreated asc')->limit(1);
		$emailAdmin = $this->db->fetchOne($select);
		if('team@dotkernel.com' == $emailAdmin)
		{
			$errors["Debug Email"][] = "Please change the email of the default admin user or deactivate him.";
		}
			
		//if the devEmails is the default one : team@dotkernel.com
		// why query db when we have it in the Dot_Model
		if(stripos($this->settings->devEmails, 'team@dotkernel.com') !== false)
		{
			$errors["Debug Email"][] = "Update the setting.devEmails value to reflect your debug email.";
		}
		
		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	/**
	 * Get any messages to display in the dashboard
	 * Each array element returned is an array with two strings: type and description
	 * @access public
	 * @return array
	 */
	public function getNotifications()
	{
		$messages = array();
		
		$adminMessages = $this->_getAdminNotifications();
		$fileMessages = $this->_getFileNotifications();
		$cacheMessages = $this->_getCacheNotifications();
		$sessionMessages = $this->_getSessionNotifications();
		$pluginMessages = $this->_getPluginNotifications();
		
		// @todo: get proper data for collations 
		$collationMessages = $this->getCollationMessages();
		$messages = array_merge_recursive($messages, $adminMessages, $fileMessages, $cacheMessages, $sessionMessages, $pluginMessages, $collationMessages );
		
		return $messages;
	}
	
	/**
	 * Get collation for all text tables and columns
	 * @access public
	 * @param $databaseName [optional] database name, if not given, the main db 
	 * @return array $collations - array with collations and charset for each table & column
	 */
	public function getTableColumnCollations($databaseName = null)
	{
		if($databaseName == null)
		{
			$databaseName = $this->config->database->params->dbname;
		}
		
		$properties =  array(
			'database'  =>'TABLE_SCHEMA' ,
			'table'     =>'TABLE_NAME' ,
			'column'    =>'COLUMN_NAME' ,
			'charset'   =>'CHARACTER_SET_NAME', 
			'collation' =>'COLLATION_NAME'
		);
		
		$select = $this->db->select()
			->from('INFORMATION_SCHEMA.COLUMNS', $properties)
			->where('TABLE_SCHEMA =?', $databaseName)
			// ignore numeric values they have no collation
			->where('COLLATION_NAME IS NOT NULL');
		return $this->db->fetchAll($select);
	}
	
	/**
	 * Get all used collations
	 * @access public
	 * @param $databaseName [optional] database name, if not given, the main db 
	 * @return array $collations - array with collations and charset for each table & column
	 */
	public function getCollations($databaseName = null)
	{
		if($databaseName == null)
		{
			$databaseName = $this->config->database->params->dbname;
		}
		
		$properties =  array(
			'database'  =>'TABLE_SCHEMA' ,
			'charset'   =>'CHARACTER_SET_NAME', 
			'collation' =>'COLLATION_NAME'
		);
		
		$select = $this->db->select()
			->from('INFORMATION_SCHEMA.COLUMNS', $properties)
			->where('TABLE_SCHEMA =?', $databaseName)
			// ignore numeric values they have no collation
			->where('COLLATION_NAME IS NOT NULL')
			->group('COLLATION_NAME');
		return $this->db->fetchAll($select);
	}
	
	/**
	 * Organize collations in $v[db][table][column] structure 
	 * 
	 * @param array $collationList
	 * @return array $organizedCollations
	 */
	public function organizeCollations($collationList)
	{
		foreach($collationList as $c)
		{
			// used $c instead of $colation for the sake of code understandability and look 
			$organizedCollations[$c['database']][$c['table']][$c['column']] = $c;
			
			unset(
				$organizedCollations[$c['database']][$c['table']][$c['column']]['database'],
				$organizedCollations[$c['database']][$c['table']][$c['column']]['table'],
				$organizedCollations[$c['database']][$c['table']][$c['column']]['column']
			);
		}
		return $organizedCollations;
	}
	
	/**
	 * Check Database Collation
	 * 
	 * @access private
	 * @return bool $isCollationValid
	 */
	private function _isDbCollationValid()
	{
		$charset = $this->config->database->params->charset;
		$databaseName = $this->config->database->params->dbname;
		$collations = $this->getCollations();

		if(count($collations) == 1)
		{
			if(stripos($collations[0]['charset'], $charset) !== FALSE && strtolower($collations[0]['charset']) == strtolower($charset))
			{
				// everything looks ok
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get Collation Related Messages 
	 * @access public
	 * @return array
	 */
	public function getCollationMessages()
	{
		$errors = $warnings = $infos= array();
		$collationList = $this->getCollations();
		$charset = $this->config->database->params->charset;
		if($this->_isDbCollationValid())
		{
			$infos['Connection Charset OK'][] = 'Connection charset: '. $charset;
			$infos['Connection Charset OK'][] = 'Detected charset: '  . $collationList[0]['charset'];
			$infos['Connection Charset OK'][] = 'Detected collation: '. $collationList[0]['collation'];
		}
		else
		{
			$errors['DB Charset Conflict'][] = 'Connection charset: '. $charset;
			$errors['DB Charset Conflict'][] =  'Detected charsets: '  ;
			foreach($collationList as $collation)
			{
				$errors['DB Charset Conflict'][] =  $collation['charset'];
			}
			$errors['DB Charset Conflict'][] =  'Using multiple charsets for the same DB connection are not recommended'  ;
			$errors['DB Charset Conflict'][] =  'The database charset should reflect the connection charset '  ;
			$errors['DB Charset Conflict'][] =  'Connection charset can be changed in application.ini '  ;
			$errors['DB Charset Conflict'][] =  'Look for <strong>database.params.charset</strong>';
			
		}
		return array('error'=>$errors,'warning'=>$warnings,'info'=>$infos);
	}
	
	/**
	 * Get a list of files in a directory
	 * @access public
	 * @param string $directory
	 * @return array
	 */
	private function _listDirectory($directory)
	{
		$result = array();
		if ($handle = opendir($directory))
		{
			while (false !== ($file = readdir($handle))) 
			{
				if ($file != "." && $file != ".." && $file != ".svn") 
				{
					$dir = $directory.'/'.$file;
					if(is_dir($dir))
					{
						$result[] = $dir;
						$list = $this->_listDirectory($dir);
						$result = array_merge($result, $list);
					}
				}
			}
			closedir($handle);
		}
		return $result;
	}
	
	/**
	 * Check if the developer e-mail is team@dotkernel.com (or the one provided)
	 * @access private
	 * @param $invalidEmailList [optional] / "team@dotkernel.com"
	 * @return bool
	 */
	private function _checkDevEmails($invalidDevEmails = 'team@dotkernel.com')
	{
		if(is_string($invalidDevEmails))
		{
			// @todo: check if an array 
			// @todo: write in the flight log
		}
		return ($this->settings->devEmails == $invalidDevEmails);
	}
	
	/**
	 * Get current php ini values
	 * 
	 * This function is an alias to ini_get_all()
	 * 
	 * @param $environment [optional] - global by default
	 * @return boolean|array()
	 */
	private function _getPhpIniValues($scope = 'global')
	{
		// global means the values from the php.ini file
		// local means locally declared values (the ones set with ini_set($key,$val) ) 
		$this->_ini = ini_get_all();
		$newArray = array();
		switch($scope)
		{
			default:
				return false;
			// If called from within a function, the return() statement immediately ends execution of the current function
			// so no breaks -- only returns
			case 'local':
			case 'global':
				foreach($this->_ini as $key => $value)
				{
					$newArray[$key] = $value[$scope.'_value'];
				}
				return $newArray;
		}
	}
	
	/**
	 * Get the Ini files with recommended correction
	 * 
	 * The default scope is local because DotKernel changes some of the 
	 * ini vars, see /configs/application.ini
	 * 
	 * @param string $scope
	 * @return multitype:|Ambigous array
	 */
	public function getIniValuesWithCorrection($scope = 'local')
	{
		$iniValues = array();
		// Recommended Values - multi-env
		$goodIniValues = $this->option->recommendedPhpIniValues->toArray();
		$recommendedIniValues = array_replace($goodIniValues['production'], $goodIniValues[APPLICATION_ENV]);
		
		// Current Values
		$allIniValues = $this->_getPhpIniValues($scope);
		// removing correct values from current ini values
		$currentIniValues = array_intersect_key($allIniValues, $recommendedIniValues);
		// making sure we only have the needed values, not all of them
		$recommendedIniValues = array_intersect_key($recommendedIniValues, $currentIniValues);
		foreach($currentIniValues as $key => $value)
		{
			// $value <=> $currentIniValues[$key]
			if($recommendedIniValues[$key] != $currentIniValues[$key] )
			{
				$iniValues[$key]['recommended'] = $recommendedIniValues[$key];
				$iniValues[$key]['current'] = $value ;
				$iniValues[$key]['access'] = $this->_ini[$key]['access'];
			}
		}
		return $iniValues;
	}
}
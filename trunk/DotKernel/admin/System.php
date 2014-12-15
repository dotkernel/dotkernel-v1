<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	 * Array with the recommended php.ini security related values
	 *
	 *
	 * @var array
	 */
	private $_recommendedIniValues =  array(
					'allow_url_fopen' => 0,
					'allow_url_include' => 0,
					
					'session.use_cookies' => 1,
					'session.use_only_cookies' => 1,
					'session.cookie_httponly' => 1,
					'session.bug_compat_42' => 0,
					'session.bug_compat_warn' => 0,
					'session.use_trans_sid' => 0,
					'session.cookie_secure' => 1,
					'session.use_strict_mode' => 1,
					
					'display_errors' => 0,
					'log_errors' => 1,
					'expose_php' => 1,
					'register_globals' => 1,
					'magic_quotes_gpc' => 0,
					'magic_quotes_runtime' => 0,
					'safe_mode' => 0,
					'register_long_arrays' => 0,
					'display_startup_errors' => 0,
					'error_reporting' => 0,
					'upload_max_filesize' => '2M',
					'post_max_size' => '8M',
					'memory_limit' => '128M',
					'asp_tags' => 0,
					'xdebug.default_enable' => 0,
					'xdebug.remote_enable' => 0
	);
	
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
			if(geoip_db_avail(GEOIP_CITY_EDITION_REV0))
			{
				$info = explode(" ",geoip_database_info(GEOIP_CITY_EDITION_REV0));
				$return['city'] = $info[0].' '.Dot_Kernel::TimeFormat($info[1]);
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
		$result["version"] = ($apcVersion===FALSE) ? "" : $apcVersion;
		$result["enabled"] = (function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE));
		return $result;
	}
	/**
	 * Get any warnings to display in the dashboard
	 * Each array element returned is an array with two strings: type and description
	 * @access public
	 * @return array
	 */
	public function getWarnings()
	{
		// warning "categories"
		$warnings = array('Security Warning'=>array(),
												'Debug Email' => array(),
												'Delete Files'=>array(),
												'Make Writable'=>array(), 
												'Make Unwritable'=>array()
		);
		
		// check that the default admin user isn't enabled
		$dotAuth = Dot_Auth::getInstance();
		$defaultAdminValid = $dotAuth->process('admin', array("username"=>"admin", "password"=>"dot"), $storeInSession = false);
		if ($defaultAdminValid)
		{
			$warnings["Security Warning"][] = "Please change the password of the oldest admin user or deactivate him";
		}
		
		// if the oldest admin have the same email team@dotkernel.com
		$select = $this->db->select()->from('admin', 'email')->where('isActive = ?', '1')->order('dateCreated asc')->limit(1);
		$emailAdmin = $this->db->fetchOne($select);
		if('team@dotkernel.com' == $emailAdmin)
		{
			$warnings["Debug Email"][] = "Please change the email of the default admin user or deactivate him.";
		}
			
		//if the devEmails is the default one : team@dotkernel.com
		// why query db when we have it in the Dot_Model  
		if(stripos($this->settings->devEmails, 'team@dotkernel.com') !== false)
		{
			$warnings["Debug Email"][] = "Update the setting.devEmails value to reflect your debug email.";
		}
		
		// check for files that should be deleted
		$filesToDelete = array("dot_kernel.sql", "readme.txt", "dk.php");
		foreach($filesToDelete as $file)
		{
			if(file_exists(APPLICATION_PATH . "/" . $file))
			{
				$warnings['Delete Files'][] = $file;
			}
		}
			
		//ignore permission warning if OS is Windows
		if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		{
			// warning if application.ini is writable
			if(is_writable(APPLICATION_PATH . "/configs/application.ini"))
			{
				$warnings["Make Unwritable"][] = 'configs/application.ini';
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
						$warnings["Make Writable"][] = $path;
					}
				}
				else
				{
					if(is_writable($path))
					{
						
						$warnings["Make Unwritable"][] = $path;
					}
				}
			}
				// info about how to add exception
			if(count($warnings["Make Unwritable"]))
			{
				$warnings["Make Unwritable"][] = '**  <em>It is possible to add your writable folders to the exclude list by adding it 
										as folders.permission[] exception in application.ini</em>';
			}
		}
		return $warnings;
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
	 * Get email transporter by field
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return array
	 */
	public function getEmailTransporterBy($field, $value)
	{ 
		$select = $this->db->select()
				->from('emailTransporter')
				->where($field.' = ?', $value)
				->limit(1);
		$result = $this->db->fetchRow($select);
		return $result;
	}	
	/**
	 * Get email transporter list
	 * @access public 
	 * @param int $page [optional]
	 * @return array
	 */
	public function getEmailTransporterList($page = 1)
	{
		$select = $this->db->select()
					->from('emailTransporter')
					->order('id');
		$dotPaginator = new Dot_Paginator($select, $page, $this->settings->resultsPerPage);
		return $dotPaginator->getData();
	}

	/**
	 * Activate/Inactivate email transporter
	 * @access public
	 * @param int $id - transporter ID
	 * @param int $isActive
	 * @return void
	 */
	public function activateEmailTransporter($id, $isActive)
	{
		$this->db->update('emailTransporter', array('isActive' => $isActive), 'id = ' . $id);
	}

	/**
	 * Delete email transporter
	 * @access public
	 * @param int $id
	 * @return void
	 */
	public function deleteEmailTransporter($id)
	{
		$this->db->delete('emailTransporter', 'id = ' . $id);
	}

	/**
	 * Update email transporter
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function updateEmailTransporter($data)
	{
		$id = $data['id'];
		unset($data['id']);
		$this->db->update('emailTransporter', $data, 'id = ' . $id);
	}

	/**
	 * Validate transporter
	 * @access public
	 * @param array $data 
	 * @return array
	 */
	public function validateEmailTransporter($data)
	{
		$integerValidator = new Zend_Validate_Int();
		$errors = array();
		if(! $integerValidator->isValid($data['port']))
		{
			array_push($errors, $this->option->errorMessage->invalidPort);
		}
		if(! $integerValidator->isValid($data['capacity']))
		{
			array_push($errors, $this->option->errorMessage->invalidCapacity);
		}
		
		if(empty($data['user']) || empty($data['pass']) || empty($data['server']))
		{
			array_push($errors, $this->option->errorMessage->emptyFields);
		}
		
		return $errors;
	}

	/**
	 * Add email transporter
	 * @access public
	 * @param array $data
	 * @return id - last insert ID
	 */
	public function addEmailTransporter($data)
	{
		$this->db->insert('emailTransporter', $data);
		return $this->db->lastInsertId();
	}

	/**
	 * Check if the developer e-mail is team@dotkernel.com (or the one provided)
	 * @access private
	 * @param $invalidEmailList [optional] / "team@dotkernel.com"
	 * @return bool
	 */
	private function _checkDevEmails($invalidDevEmails = 'team@dotkernel.com')
	{
		return ($this->settings->devEmails == $invalidDevEmails);
	}
	
	/**
	 * Get current php ini values
	 * 
	 * This function is an alias to ini_get_all()
	 * @param $environment [optional] - global by default
	 * @return boolean|array()
	 */
	private function _getPhpIniValues($scope = 'global')
	{
		// global means the values from the php.ini file
		// local means locally declared values (the ones set with ini_set($key,$val) ) 
		$ini = ini_get_all();
		$newArray = array();
		switch($scope)
		{
			default:
				return FALSE;
			// If called from within a function, the return() statement immediately ends execution of the current function
			case 'local':
			case 'global':
				foreach($ini as $key => $value)
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
		// Recommended Values
		$recommendedIniValues = $this->_recommendedIniValues;
		// Current Values
		$allIniValues = $this->_getPhpIniValues($scope);
		// removing correct values
		$currentIniValues = array_intersect_key($allIniValues, $recommendedIniValues);
		$recommendedIniValues = array_intersect_key($recommendedIniValues, $currentIniValues);
		
		foreach($currentIniValues as $key => $value)
		{
			// $value <=> $currentIniValues[$key]
			if($recommendedIniValues[$key] != $currentIniValues[$key] )
			{
				$iniValues[$key]['recommended'] = $recommendedIniValues[$key];
				$iniValues[$key]['current'] = $value ;
			}
		}
		return $iniValues;
	}
}
<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	public static function getHostname()
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
	 *    version: a string with the version of APC that is currently installed or an empty string if it's not installed
	 *    enabled: boolean
	 * @access public
	 * @return array
	 */
	public function getAPCInfo()
	{
		$result = array();
		$result["version"] = (phpversion('apc')===FALSE) ? "" : phpversion('apc');
		$result["enabled"] = (function_exists('apc_cache_info') && (@apc_cache_info() !== FALSE));
		return $result;
	}
	/**
	 * Get information about the Wurfl library installed
	 * Returns an array with the following elements:
	 *    cacheBuilt: the date the last time the cache was manually built,
	 *                stored in the setting table
	 *    date: the last modified date of the wurfl xml file
	 * 
	 * @access public
	 * @return array
	 */
	public function getWurflInfo()
	{
		$result = array();
		$wurfl = Dot_UserAgent_Wurfl::getInstance();
		$wurflInfo = $wurfl->getWurflVersion();
		$result['xmlFileDate']    = $wurflInfo['xmlFileDate'];
		$result['cacheDate']      = $wurflInfo['cacheDate'];
		$result['apiVersion']     = $this->config->resources->useragent->wurflapi->api_version;
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
		$warnings = array();
		
		// check for files that should be deleted
		$filesToDelete = array(
			"dot_kernel.sql",
			"readme.txt",
			"dk.php"
		);
		foreach ($filesToDelete as $file)
		{
			if (file_exists(APPLICATION_PATH."/".$file))
			{
				$warnings[] = array('type'=>'please delete', 'description'=>$file);
			}
		}

		//ignore permission warning if OS is Windows
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') 
		{
			// warning if application.ini is not 644		
			if(substr(decoct(fileperms(APPLICATION_PATH."/configs/application.ini")),-3) != '644')
			{//convert the fileperms result from decimal to octal, and take only the last 3 chars
				$warnings[] = array('type'=>'change permission to 644', 'description'=>'configs/application.ini');
			}
			
			// warning for all folders > 755 	
			$folderException = $this->config->folders->permission->toArray();	
			$folders = $this->_listDirectory(APPLICATION_PATH);
			foreach ($folders as $path)
			{
				$skip = false;
				foreach($folderException as $exception)
				{
					if (strpos($path, $exception) !== false)
					{
						$skip = true;
						break;
					}
				}
				if(!$skip && intval(substr(decoct(fileperms($path)),-3)) > 755)
				{
					$warnings[] = array('type'=>'change permission to 755', 'description'=>$path);
				}
			}
		}
							
		// add any other warnings to $warnings here
		return $warnings;
	}
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
		$this->db->update('emailTransporter', array('isActive' => $isActive), 'id = '.$id);
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
		unset ($data['id']);
		$this->db->update('emailTransporter', $data, 'id = '.$id);
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
		$errors=array();
		if (!$integerValidator->isValid($data['port']))
		{
			array_push($errors, $this->option->errorMessage->invalidPort);
		}
		if (!$integerValidator->isValid($data['capacity']))
		{
			array_push($errors, $this->option->errorMessage->invalidCapacity);
		}
		
		if (empty($data['user']) || empty($data['pass']) || empty($data['server']))
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
}

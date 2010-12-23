<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
class System
{
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct()
	{
		$this->db = Zend_Registry::get('database');
		$this->option = Zend_Registry::get('option');
		$this->settings = Zend_Registry::get('settings');
		$this->config = Zend_Registry::get('configuration');
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
		$result['cacheBuilt'] = $this->settings->wurflCacheBuilt;
		
		$wurflConfigFile = $this->config->resources->useragent->wurflapi->wurfl_config_file;
		$wurflConfig = new Zend_Config_Xml($wurflConfigFile);
		$wurflConfigArray = $wurflConfig->wurfl->toArray();
		$wurflZipPath = dirname($wurflConfigFile)."/".$wurflConfigArray['main-file'];
		$timestamp = filemtime($wurflZipPath);
		$result['date'] = strftime($this->settings->timeFormatLong,$timestamp);
		
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
	 * @param array $values 
	 * @return array
	 */
	public function validateEmailTransporter($data)
	{
		$validator = new Zend_Validate_Int();
		$errors=array();
		if (!$validator->isValid($data['port']))
		{
			array_push($errors, $this->option->errorMessage->invalidPort);
		}
		if (!$validator->isValid($data['capacity']))
		{
			array_push($errors, $this->option->errorMessage->invalidCapacity);
		}
		return $errors;
	}
	/**
	 * Add email transporter
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function addEmailTransporter($data)
	{
		$this->db->insert('emailTransporter', $data);
	}
}

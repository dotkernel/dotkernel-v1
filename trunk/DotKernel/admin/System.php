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
	 * Get top 10 country user logins
	 * @access public
	 * @param array $logins
	 * @return array
	 */
	public function getCountryUserLogin($logins)
	{
		$countryName = array();
		$countryCount = array();
		$dotGeoip = new Dot_Geoip();
		foreach ($logins as $v)
		{
			$country = $dotGeoip->getCountryByIp($v['ip']);
			if(array_key_exists($country[0], $countryCount))
			{
				$countryCount[$country[0]]++;
			}
			else
			{
				 $countryCount[$country[0]] = 1;
				 $countryName[$country[0]] = $country[1];
			}
		}
		arsort($countryCount);
		$countSum = array_sum($countryCount);
		$i = 1;
		$data['other'] = array('count' => 0, 'countPercent' => 0,'name' => 'Others');
		foreach ($countryCount as $code => $count)
		{
			$countPercent = round($count * 100 / $countSum, 2);
			if($i >= $this->option->countCountryUserLogin)
			{
				$data['other']['countPercent'] += $countPercent; 
				$data['other']['count'] += $count; 
			}
			else
			{
				$data[$code]['countPercent'] = $countPercent; 
				$data[$code]['count'] = $count; 
				$data[$code]['name'] = ucfirst($countryName[$code]);
				
			}
			$i++;
		}
		if(!$data['other']['count'])
		{
			unset($data['other']);
		}
		return $data;
	}
	/**
	 * Get GeoIp Version release
	 * Return an array with keys "country" & "city"
	 * @access public
	 * @return array
	 */
	public function getGeoIpVersion()
	{
		$return = array('country' => '-', 'city' => '-');
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
	 * @return array(array(), Zend_Paginator_Adapter())
	 */
	public function getEmailTransporterList($page = 1)
	{
		$select = $this->db->select()
		           ->from('emailTransporter')
		           ->order('id');        
		$paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($select);
		($page == 1) ? 
		  $select->limit($this->settings->resultsPerPage) : 
		  $select->limit($this->settings->resultsPerPage, ($page-1)*$this->settings->resultsPerPage);
		          
		$data = $this->db->fetchAll($select);
		return array('data'=> $data,'paginatorAdapter'=> $paginatorAdapter);
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

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
		return $data;
	}
}

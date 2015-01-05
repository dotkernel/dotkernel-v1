<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Extract all informations from OpCache
 * @category   DotKernel
 * @package    DotLibrary
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_OpCache
{
	/**
	 * Constructor,
	 * @access public
	 * @return Dot_OpCache
	*/
	public function __construct()
	{
		$this->config = Zend_Registry::get('configuration');
	}
	/**
	 * Get OpCache configuration
	 * @param void
	 * @access public
	 * @return array
	 */
	public function configuration()
	{
		$config = null;
		if (function_exists('opcache_get_configuration'))
		{
			$config = opcache_get_configuration();
		}
		return $config;
	}
	
	/**
	 * Get OpCache status data
	 * @param void
	 * @access public
	 * @return array
	 */
	public function status()
	{
		$status = null;
		if (function_exists('opcache_get_status'))
		{
			$status = opcache_get_status();
		}
		return $status;
	}
	
	/**
	 * Transform sizes in bytes
	 * @param void
	 * @access public
	 * @return array
	 */
	public function bsize($s)
	{
		foreach (array('','K','M','G') as $i => $k)
		{
			if ($s < 1024) break;
			$s/=1024;
		}
		return sprintf("%5.1f %sB", $s, $k);
	}
	
	/**
	 * Return the widget data: Memory Piechart
	 * @access public
	 * @param void
	 * @return json
	 */
	public function returnMemoryPiechart()
	{
		$data = array();
		$status = $this->status();
		// pie chart data
		if ( $status !== null)
		{
			$data[] = array('label' => 'Free ' . $this->bsize($status['memory_usage']['free_memory']),
							'data' => $status['memory_usage']['free_memory']);
			$data[] = array('label' => 'Wasted ' . $this->bsize($status['memory_usage']['wasted_memory']),
							'data' => $status['memory_usage']['wasted_memory']);
			$data[] = array('label' => 'Used ' . $this->bsize($status['memory_usage']['used_memory']),
							'data' => $status['memory_usage']['used_memory']);
		}
		$jsonString = Zend_Json::encode($data);
		return $jsonString;
	}
	
	/**
	 * Return the widget data: Keys Piechart
	 * @access public
	 * @param void
	 * @return json
	 */
	public function returnKeysPiechart()
	{
		// pie chart data
		$data = array();
		$status = $this->status();
		if ( $status !== null)
		{
			$data[] = array('label' => 'Free ' . ($status['opcache_statistics']['max_cached_keys'] - $status['opcache_statistics']['num_cached_keys']),
							'data' => $status['opcache_statistics']['max_cached_keys'] - $status['opcache_statistics']['num_cached_keys']);
			$data[] = array('label' => 'Wasted ' . ($status['opcache_statistics']['num_cached_keys'] - $status['opcache_statistics']['num_cached_scripts']),
							'data' => $status['opcache_statistics']['num_cached_keys'] - $status['opcache_statistics']['num_cached_scripts']);
			$data[] = array('label' => 'Used ' . $status['opcache_statistics']['num_cached_scripts'],
							'data' => $status['opcache_statistics']['num_cached_scripts']);
		}
		$jsonString = Zend_Json::encode($data);
		return $jsonString;
	}
	
	/**
	 * Return the widget data: Hits Piechart
	 * @access public
	 * @param void
	 * @return json
	 */
	public function returnHitsPiechart()
	{
		$data = array();
		$status = $this->status();
		if ( $status !== null)
		{
			$data[] = array('label' => 'Hits ' . $status['opcache_statistics']['hits'],
							'data' => $status['opcache_statistics']['hits']);
			$data[] = array('label' => 'Misses ' . $status['opcache_statistics']['misses'],
							'data' => $status['opcache_statistics']['misses']);
			$data[] = array('label' => 'Blacklist ' . $status['opcache_statistics']['blacklist_misses'],
							'data' => $status['opcache_statistics']['blacklist_misses']);
		}
		$jsonString = Zend_Json::encode($data);
		return $jsonString;
	}

}
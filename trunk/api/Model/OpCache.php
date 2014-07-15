<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Api
 * @subpackage OpCache
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 * @author     DotKernel Team <team@dotkernel.com>
*/

class OpCache extends Dot_Model
{
	/**
	 * Api constructor
	 * @access public 
	 * @return Dot_Model_Api
	 */
	public function __construct()
	{
		$this->opCache = new Dot_OpCache();
	}
	
	/**
	 * Get OpCache configuration
	 * @param void
	 * @access public
	 * @return json
	 */
	public function opCacheStatus()
	{
		$status = $this->opCache->status();
		if ($status !== NULL)
		{
			if (isset($status['opcache_statistics']['start_time']))
			{
				$startTime = date("H:i:s d-m-Y", $status['opcache_statistics']['start_time']);
			}
			else
			{
				$startTime = NULL;
			}
			if (isset($status['opcache_statistics']['last_restart_time']))
			{
				$restartTime = date("H:i:s d-m-Y", $status['opcache_statistics']['last_restart_time']);
			}
			else
			{
				$restartTime = NULL;
			}
			
			$data = array();
			$data[] = array('result' => 'ok');
			$data[] = array('response' => array(
				'all_memory' => $this->opCache->bsize($status['memory_usage']['free_memory'] + $status['memory_usage']['used_memory'] + $status['memory_usage']['wasted_memory']),
				'free_memory' => $this->opCache->bsize($status['memory_usage']['free_memory']),
				'used_memory' => $this->opCache->bsize($status['memory_usage']['used_memory']),
				'wasted_memory' => $this->opCache->bsize($status['memory_usage']['wasted_memory']),
				'cached_scripts' => $status['opcache_statistics']['num_cached_scripts'],
				'cached_keys' => $status['opcache_statistics']['num_cached_keys'],
				'max_cached_keys' => $status['opcache_statistics']['max_cached_keys'],
				'hits' => $status['opcache_statistics']['hits'],
				'misses' => $status['opcache_statistics']['misses'],
				'blacklist_misses' => $status['opcache_statistics']['blacklist_misses'],
				'miss_ratio' => round($status['opcache_statistics']['blacklist_miss_ratio'], 2),
				'opcache_hit_rate' => round($status['opcache_statistics']['opcache_hit_rate'], 2) . '%',
				'start_time' => $startTime,
				'last_restart' => $restartTime,
				'oom_restart' => $status['opcache_statistics']['oom_restarts'],
				'hash_restarts' => $status['opcache_statistics']['hash_restarts'],
				'manual_restarts' => $status['opcache_statistics']['manual_restarts'],
				'currently_wasted' => round($status['memory_usage']['current_wasted_percentage'], 2) . '%'
			));
		}
		else
		{
			$data = array();
			$data[] = array('result' => 'error');
			$data[] = array('response' => 'OpCache Not Installed!');
		}
		
		$jsonString = Zend_Json::encode($data);
		return $jsonString;
	}
}
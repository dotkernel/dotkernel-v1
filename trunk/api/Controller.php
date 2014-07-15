<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Api
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 * @author     DotKernel Team <team@dotkernel.com>
 */

if (!$registry->configuration->api->params->enable)
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

if (isset($registry->action))
{
	switch ($registry->action)
	{
		case 'version':
			$data = array();
			$data[] = array('result' => 'ok');
			$data[] = array('response' => Dot_Kernel::VERSION);
			$jsonString = Zend_Json::encode($data);
			echo $jsonString;
		break;
		
		case 'opcache':
			if ($registry->configuration->api->params->key == $registry->arguments['key'])
			{
				$opCacheModel = new Api_Model_OpCache();
				echo $opCacheModel->opCacheStatus();
			}
			else
			{
				header("HTTP/1.0 401 Unauthorized");
				$data = array();
				$data[] = array('result' => 'error');
				$data[] = array('response' => "Invalid Key");
				$jsonString = Zend_Json::encode($data);
				echo $jsonString;
			}
		break;
	
		default:
			$data = array();
			$data[] = array('result' => 'error');
			$data[] = array('response' => "Action doesn't exist");
			$jsonString = Zend_Json::encode($data);
			echo $jsonString;
		break;
	}
}
else
{
	$data = array();
	$data[] = array('result' => 'error');
	$data[] = array('response' => "Action doesn't exist");
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
}
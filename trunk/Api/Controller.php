<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Api
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 * @author     DotKernel Team <team@dotkernel.com>
 */

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
		$opCacheModel = new Api_Model_OpCache();
		echo $opCacheModel->opCacheStatus();
	break;

	default:
		Api_Model_Header::setHeaderByCode(501);
		$data = array();
		$data[] = array('result' => 'error');
		$data[] = array('response' => "Action doesn't exist");
		$jsonString = Zend_Json::encode($data);
		echo $jsonString;
		exit;
	break;
}
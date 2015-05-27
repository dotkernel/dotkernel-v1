<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* System Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$systemView = new System_View($tpl);
$systemModel = new System();
// switch based on the action, NO default action here
$pageTitle = $option->pageTitle->action->{$registry->requestAction};
switch ($registry->requestAction)
{
	case 'dashboard':
		$mysqlVersion = $systemModel->getMysqlVersion();
		$geoIpVersion = $systemModel->getGeoIpVersion();
		$warnings = $systemModel->getWarnings(array());
		$apcInfo = $systemModel->getAPCInfo();
		//	Ini Values
		$iniValues = $systemModel->getIniValuesWithCorrection();
		$cacheInfo = Dot_Cache::getCacheInfo();
		$systemView->dashboard('dashboard', $mysqlVersion, $apcInfo, $geoIpVersion, $warnings, $iniValues, $cacheInfo);
	break;
	case 'settings':
		// list settings values
		$data = $systemModel->getSettings();
		if(isset($registry->request['update']) && $registry->request['update'] == 'done')
		{
			$registry->session->message['txt'] = $option->infoMessage->settingsUpdate;
			$registry->session->message['type'] = 'info';
		}
		$systemView->displaySettings('settings', $data);
	break;
	case 'settings-update':
		$data = array();
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			// changes were made to checkUserToken
			// see: Dot_Auth::checkUserToken($userToken, $userType='admin')
			// see: IndexController.php : $userToken
			if( !Dot_Auth::checkUserToken($userToken) )
			{
				// remove the identity
				$dotAuth = Dot_Auth::getInstance();
				$dotAuth->clearIdentity('admin');
				// warn the user
				$session->message['txt'] = $option->warningMessage->tokenExpired; 
				$session->message['type'] = 'warning';
				// go to log in 
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestController. '/login');
				exit;
			}
			$systemModel->updateSettings($_POST);
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule 
				. '/' . $registry->requestController. '/settings/update/done');
			exit;
		}
	break;
	case 'phpinfo':
		// display phpinfo()
		$systemView->showPHPInfo('phpinfo');
	break;
	case 'apc-info':
		// display APC or APCu
		$apcu = null;
		if(phpversion('apcu')) 
		{
			$apcu = 'u';
		}
		$systemView->showAPCInfo($apcu);
	break;
	case 'delete-key':
		$result = array("success" => false, "message" => "An error occured, please try again.");
		if(!isset($_POST['key']) || !isset($_POST['userToken']) || !Dot_Auth::checkUserToken($_POST['userToken']))
		{
			echo Zend_Json::encode($result);
			exit;
		}
		Dot_Cache::remove($_POST['key']);
		$result = array('succes'=>'true');
		echo Zend_Json::encode($result);
		exit;
		
	case 'clear-cache':
		$result = array("success" => false, "message" => "An error occured, please try again.");
		if(!isset($_POST['userToken']) || !Dot_Auth::checkUserToken($_POST['userToken']))
		{
			echo Zend_Json::encode($result);
			exit;
		}
		Dot_Cache::clean('all');
		$result = array('succes'=>'true');
		echo Zend_Json::encode($result);
		exit;
}
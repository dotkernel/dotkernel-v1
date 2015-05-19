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
		$systemView->dashboard('dashboard', $mysqlVersion, $apcInfo, $geoIpVersion, $warnings, $iniValues);
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
}
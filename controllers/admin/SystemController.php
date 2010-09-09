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
* System Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$systemView = new System_View($tpl);
$systemModel = new System();
// switch based on the action, NO default action here
$pageTitle = $option->pageTitle->action->{$requestAction};
switch ($requestAction)
{
	case 'dashboard':		
		$userModel = new User(); 
		$userLogins = $userModel->getLogins(0,0);
		$userCountry = $systemModel->getCountryUserLogin($userLogins['data']);
		$mysqlVersion = $systemModel->getMysqlVersion();
		$systemView->dashboard('dashboard', $mysqlVersion, $userCountry);
	break;
	case 'settings':
		// list settings values
		$data = $systemModel->getSettings();	
		if(isset($request['update']) && $request['update'] == 'done')
		{			
				$session->message['txt'] = $option->infoMessage->settingsUpdate;
				$session->message['type'] = 'info';
		}
		$systemView->displaySettings('settings', $data);
	break;
	case 'settings-update':
		// update settings value
		$data = array();
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			unset($_POST['send']);
			$systemModel->updateSettings($_POST);			
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/settings/update/done');
			exit;
		}
	break;
	case 'phpinfo':
		// display phpinfo()
		$systemView->showPHPInfo('phpinfo');
	break;	
}
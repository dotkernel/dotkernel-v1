<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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
$pageTitle = $scope->pageTitle->action->{$requestAction};
switch ($requestAction)
{
	case 'dashboard':		
		$systemView->dashboard('dashboard');
	break;
	case 'settings':
		$data = $systemModel->listSettings();	
		$message = (isset($request['update']) && $request['update'] == 'done') ? $scope->message->settingsUpdate : '';
		$systemView->displaySettings('settings', $data, $message);
	break;
	case 'settings-update':
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
		$systemView->showPHPInfo('phpinfo');
	break;	
}



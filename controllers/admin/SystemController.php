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
// All actions MUST return this variable
$pageTitle = '';

// instantiate  AuthUser object
$systemView = new System_View($tpl);
$systemModel = new System();
// switch based on the action, NO default action here
switch ($requestAction)
{
	case 'dashboard':
		$pageTitle = 'Dashboard';		
		$systemView->dashboard('dashboard');
	break;
	case 'settings':
		$pageTitle = 'Settings';
		$data = $systemModel->listSettings();	
		$systemView->displaySettings('settings', $data);
	break;
	case 'settings-update':
		$data = array();
		$error = array();
		$pageTitle = 'Update Settings';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			// We don't do validation, only addslashes, because there 
			// are to many fields, with different value types
			unset($_POST['send']);
			$data = array_map('addslashes', $_POST);
			$systemModel->updateSettings($data);
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/settings');
				exit;
		}
		
	break;
	case 'phpinfo':
		phpinfo();
		exit;
	break;	
}



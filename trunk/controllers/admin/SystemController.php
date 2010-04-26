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
switch ($requestAction)
{
	case 'dashboard':
		$pageTitle = 'Dashboard';		
		$systemView->dashboard('dashboard');
	break;
	case 'settings':
		$pageTitle = 'Settings';
		$data = $systemModel->listSettings();	
		$message = (isset($request['update']) && $request['update'] == 'done') ? 'System Settings was updated.' : '';
		$systemView->displaySettings('settings', $data, $message);
	break;
	case 'settings-update':
		$data = array();
		$error = array();
		$pageTitle = 'Update Settings';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			unset($_POST['send']);
			$systemModel->updateSettings($_POST);			
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/settings/update/done');
			exit;
		}
	break;
	case 'phpinfo':
		$pageTitle = 'PHP Info';	
		$systemView->showPHPInfo('phpinfo');
	break;	
}



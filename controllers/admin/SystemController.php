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
$pageTitle = $option->pageTitle->action->{$registry->route['action']};
switch ($registry->route['action'])
{
	case 'dashboard':		
		$mysqlVersion = $systemModel->getMysqlVersion();
		$geoIpVersion = $systemModel->getGeoIpVersion();
		$systemView->dashboard('dashboard', $mysqlVersion, $geoIpVersion);
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
	case 'transporter-list':
		$page = (isset($request['page']) && $request['page'] > 0) ? $request['page'] : 1;
		$transporters = $systemModel->getEmailTransporterList($page);		
		$systemView->listEmailTransporter('transporter-list', $transporters, $page);	 
	break;
	case 'transporter-activate':
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		$systemModel->activateEmailTransporter($id, $isActive);		

		$transporters = $systemModel->getEmailTransporterList($page);
		$session->useAjaxView = true; 
		$route['action'] = 'transporter-list';
		$registry->route = $route;
		$systemView->listEmailTransporter('transporter-list', $transporters, $page, true);
	break;
	case 'transporter-delete':
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{ 
			if ('on' == $_POST['confirm'])
			{
				$systemModel->deleteEmailTransporter($request['id']);
				$session->message['txt'] = $option->infoMessage->transporterDelete;
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $option->infoMessage->noTransporterDelete;
				$session->message['type'] = 'info';
			}
		 header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/transporter-list/');
			 exit;	 
		}
		$data = $systemModel->getEmailTransporterBy('id', $request['id']);
		// delete page confirmation
		$systemView->details('transporter-delete', $data);	
	break;
	case 'transporter-update':
		// display form and update user
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			$data=$_POST;
			unset($data["send"]);
			
			$error=$systemModel->validateEmailTransporter($data);

			if(empty($error))
			{
				// no error - then update
				$data["id"]=$request["id"];
				$systemModel->updateEmailTransporter($data);
				$session->message['txt'] = $option->infoMessage->transporterUpdate;
				$session->message['type'] = 'info';
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/transporter-list/');
				exit;
			}
			else
			{
				$session->message['txt'] = $error;
				$session->message['type'] = 'error';
			}
		}
		$data = $systemModel->getEmailTransporterBy('id', $request['id']);
		$systemView->details('transporter-update',$data); 
	break;
	case 'transporter-add':
		$page = $_POST["page"];
		unset($_POST["page"]);
		$data=$_POST;
		unset($data["send"]);

		$error=$systemModel->validateEmailTransporter($data);
		
		if (empty($error))
		{
			$systemModel->addEmailTransporter($data);
			$session->message['txt'] = $option->infoMessage->transporterAdd;
			$session->message['type'] = 'info';
			$data=null;
		}else{
			$session->message['txt'] = $error;
			$session->message['type'] = 'error';
		}

		$transporters = $systemModel->getEmailTransporterList($page);
		$transporters['form']=$data;
		$session->useAjaxView = true; 
		$route['action'] = 'transporter-list';
		$registry->route = $route;
		$systemView->listEmailTransporter('transporter-list', $transporters, $page, true, $error);
	break;
}
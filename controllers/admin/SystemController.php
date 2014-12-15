<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
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
		// update settings value
		$data = array();
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			// changes were made to checkUserToken
			// see: Dot_Auth::checkUserToken($userToken, $userType='admin')
			// see: IndexController.php : $userToken
			if( !Dot_Auth::checkUserToken($userToken) ) // if the admin is not logged redir to 
			{
				// remove the identity
				$dotAuth = Dot_Auth::getInstance();
				$dotAuth->clearIdentity('admin');
				// warn the user
				$session->message['txt'] = $option->warningMessage->tokenExpired; 
				$session->message['type'] = 'warning';
				// log in 
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
		$apcu = NULL;
		if(phpversion('apcu')) 
		{
			$apcu = 'u';
		}
		$systemView->showAPCInfo($apcu);
	break;
	case 'transporter-list':
		$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
		$transporters = $systemModel->getEmailTransporterList($page);
		$systemView->listEmailTransporter('transporter-list', $transporters, $page);
	break;
	case 'transporter-activate':
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		$systemModel->activateEmailTransporter($id, $isActive);

		$result = array(
			"success" => true,
			"id" => $id,
			"isActive" => intval($isActive)
		);
		
		echo Zend_Json::encode($result);
		exit;
	break;
	case 'transporter-delete':
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{ 
			if ('on' == $_POST['confirm'])
			{
				$systemModel->deleteEmailTransporter($registry->request['id']);
				$registry->session->message['txt'] = $option->infoMessage->transporterDelete;
				$registry->session->message['type'] = 'info';
			}
			else
			{
				$registry->session->message['txt'] = $option->infoMessage->noTransporterDelete;
				$registry->session->message['type'] = 'info';
			}
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule 
			        . '/' . $registry->requestController. '/transporter-list/');
			exit;
		}
		$data = $systemModel->getEmailTransporterBy('id', $registry->request['id']);
		// delete page confirmation
		$systemView->details('transporter-delete', $data);
	break;
	case 'transporter-update':
		// display form and update user
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			$data=$_POST;
			unset($data["send"]);
			
			$error=$systemModel->validateEmailTransporter($data);

			if(empty($error))
			{
				// no error - then update
				$data["id"]=$registry->request["id"];
				$systemModel->updateEmailTransporter($data);
				$registry->session->message['txt'] = $option->infoMessage->transporterUpdate;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule
				         . '/' . $registry->requestController. '/transporter-list/');
				exit;
			}
			else
			{
				$registry->session->message['txt'] = $error;
				$registry->session->message['type'] = 'error';
			}
		}
		$data = $systemModel->getEmailTransporterBy('id', $registry->request['id']);
		$systemView->details('transporter-update',$data); 
	break;
	case 'transporter-add':
		$data = $_POST;
		$error=$systemModel->validateEmailTransporter($data);

		$result = array();

		if (empty($error))
		{
			$id = $systemModel->addEmailTransporter($data);
			$data = $systemModel->getEmailTransporterBy('id', $id);
			$result['data'] = $data;
			$result['success'] = true;
			$result['message'] = array($option->infoMessage->transporterAdd);
			$result['row'] = $systemView->getTransporterRow($data);
		}
		else
		{
			$result['success'] = false;
			$result['message'] = $error;
		}
		
		echo Zend_Json::encode($result);
		exit();
	break;
}
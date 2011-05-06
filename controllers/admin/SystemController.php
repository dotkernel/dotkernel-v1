<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
		$wurflInfo = $systemModel->getWurflInfo();
		$warnings = $systemModel->getWarnings($wurflInfo);
		$apcInfo = $systemModel->getAPCInfo();
		$systemView->dashboard('dashboard', $mysqlVersion, $apcInfo, $geoIpVersion, $wurflInfo, $warnings);
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
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			unset($_POST['send']);
			$systemModel->updateSettings($_POST);
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/settings/update/done');
			exit;
		}
	break;
	case 'build-wurfl-cache':
		// creates Zend_Http_UserAgent objects with the 15 most common mobile user agents to build the cache
		$userAgents = array(
			'Mozilla/5.0 (Linux; U; Android 1.6; en-us; T-Mobile G1 Build/DMD64) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1',
			'sam-r350 UP.Browser/6.2.3.8 (GUI) MMP/2.0',
			'Mozilla/4.0 (BREW 3.1.5; U; en-us; Sanyo; NetFront/3.5.1/AMB) Boost SCP6760',
			'HUAWEI-M750/001.00 ACS-NetFront/3.2',
			'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Mobile/8B117',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; T-Mobile_LEO; Windows Phone 6.5)',
			'Mozilla/4.0 (BREW 3.1.5; U; en-us; Sanyo; NetFront/3.5.1/AMB) Boost SCP3810',
			'Mozilla/5.0 (iPod; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Mobile/8B117',
			'Mozilla/5.0 (iPod; U; CPU iPhone OS 3_1_3 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Mobile/7E18',
			'Mozilla/4.0 (BREW 3.1.5; U; en-us; Sanyo; Polaris/6.0/AMB) Boost SCP-2700',
			'sam-r560 UP.Browser/6.2.3.8 (GUI) MMP/2.0',
			'LGE-MN240/1.0 UP.Browser/6.2.3.8 (GUI) MMP/2.0',
			'Mozilla/5.0 (rv:1.3; BREW 3.1.5; en)/400x240 sam-r860 like Gecko/20080531 NetFront/3.5',
			'sam-r100 UP.Browser/6.2.3.8 (GUI) MMP/2.0',
			'Cricket-A200/1.0 UP.Browser/6.3.0.7 (GUI) MMP/2.0'
		);

		foreach ($userAgents as $ua)
		{
			$device = new Zend_Http_UserAgent($registry->configuration->resources->useragent);
			$device->setUserAgent($ua);
			$d=$device->getDevice();
		}
		$settings = Zend_Registry::get('settings');
		$systemModel->updateSettings(array('wurflCacheBuilt'=>strftime($settings->timeFormatLong)));
		header('Location: '.$registry->configuration->website->params->url. '/admin');
		exit();
	break;
	case 'phpinfo':
		// display phpinfo()
		$systemView->showPHPInfo('phpinfo');
	break;
	case 'apc-info':
		// display apc.php
		$systemView->showAPCInfo();
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
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
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
		header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/transporter-list/');
			 exit;
		}
		$data = $systemModel->getEmailTransporterBy('id', $registry->request['id']);
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
				$data["id"]=$registry->request["id"];
				$systemModel->updateEmailTransporter($data);
				$registry->session->message['txt'] = $option->infoMessage->transporterUpdate;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/transporter-list/');
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
		}else{
			$result['success'] = false;
			$result['message'] = $error;
		}
		
		echo Zend_Json::encode($result);
		exit();
	break;
}
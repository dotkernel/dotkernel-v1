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
* User Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

// instantiate classes related to User module: model & view
$userModel = new User(); 
$userView = new User_View($tpl);
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$registry->route['action']};
switch ($registry->route['action'])
{
	case 'list':
		// list users
		$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
		$users = $userModel->getUserList($page);		
		$userView->listUser('list', $users, $page);		
	break;
	case 'add':
		// display form and add new user
		$data = $_POST;
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			Dot_Kernel::checkUserToken();
			// POST values that will be validated
			$values = array('username' => 
								array('username' => $_POST['username']
									 ),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1',
											'isActive' => $_POST['isActive']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'add', 'values' => $values));		
			if($dotValidateUser->isValid())
			{
				// no error - then add user
				$userModel->addUser($dotValidateUser->getData());				
				$registry->session->message['txt'] = $option->infoMessage->accountAdd;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/list/');
				exit;					
			}
			else
			{				
				$registry->session->message['txt'] = $dotValidateUser->getError();
				$registry->session->message['type'] = 'error';
			}
			$data = $dotValidateUser->getData();		
		}
		$userView->details('add',$data);		
	break;
	case 'update':
		// display form and update user
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{				
			Dot_Kernel::checkUserToken();
			// POST values that will be validated						
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
									 ),
							'username' => array('username' => $_POST['username']),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1',
											'isActive' => $_POST['isActive']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'update', 'values' => $values, 'userId' => $registry->request['id']));
			if($dotValidateUser->isValid())			
			{
				// no error - then update user
				$data = $dotValidateUser->getData();
				$data['id'] = $registry->request['id'];				
				$userModel->updateUser($data);
				$registry->session->message['txt'] = $option->infoMessage->accountUpdate;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/list/');
				exit;				
			}
			else
			{
				$registry->session->message['txt'] = $dotValidateUser->getError();
				$registry->session->message['type'] = 'error';
			}
		}
		$data = $userModel->getUserBy('id', $registry->request['id']);
		$userView->details('update',$data);	
	break;
	case 'activate':
		// activate/inactivate user account
		// this action is called from ajax request dojo.xhrPost()
		Dot_Kernel::checkUserToken();
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		$values = array('enum' => array('0' => '0,1', 'isActive' => $isActive));
		$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'activate', 'values' => $values));
		if($dotValidateUser->isValid())		
		{	
			$data = $dotValidateUser->getData();
			// no error - then change active value of user
			$userModel->activateUser($id, $data['isActive']);		
		}
		else
		{
			$registry->session->message['txt'] = $option->errorMessage->trickUserError;
			$registry->session->message['type'] = 'error';
		}
		$users = $userModel->getUserList($page);
		$registry->session->useAjaxView = true;
		$route = $registry->route;
		$route['action'] = 'list';
		$registry->route = $route;
		$userView->listUser('list', $users, $page, true);
	break;
	case 'delete':
		// display confirmation form and delete user account
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{	
			Dot_Kernel::checkUserToken();
			if ('on' == $_POST['confirm'])
			{
				// delete user
				$userModel->deleteUser($registry->request['id']);
				$registry->session->message['txt'] = $option->infoMessage->accountDelete;
				$registry->session->message['type'] = 'info';
			}
			else
			{
				$registry->session->message['txt'] = $option->infoMessage->noAccountDelete;
				$registry->session->message['type'] = 'info';
			}
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/list/');
			exit;				
		}
		$data = $userModel->getUserBy('id', $registry->request['id']);
		// delete page confirmation
		$userView->details('delete', $data);	
	break;
	case 'send-password':
		// send an email with the password to the selected user
		$data = array();
		$error = array();
		if ($registry->request['id'] > 0)
		{
			// send user password 
			$userModel->sendPassword($registry->request['id']);				
		}
		else
		{
			$registry->session->message['txt'] = $option->errorMessage->emailNotSent;
			$registry->session->message['type'] = 'error';
		}
		header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/list/');
		exit;		
	break;
	case 'logins':
		// list user logins
		$id = (isset($registry->request['id'])) ? (int)$registry->request['id'] : 0;		
		$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
		$browser = (isset($registry->request['browser'])) ? $registry->request['browser'] : '';
		$loginDate = (isset($registry->request['loginDate'])) ? $registry->request['loginDate'] : '';
		$sortField = (isset($registry->request['sort']) && in_array($registry->request['sort'], array('username', 'dateLogin'))) ? $registry->request['sort'] : 'dateLogin';
		$orderBy = (isset($registry->request['order']) && in_array($registry->request['order'], array('asc', 'desc'))) ? $registry->request['order'] : 'desc';
		$logins = $userModel->getLogins($id, $page, $browser, $loginDate, $sortField, $orderBy);
		$userView->loginsUser('logins', $logins, $page, $browser, $loginDate, $sortField, $orderBy);
	break;
}
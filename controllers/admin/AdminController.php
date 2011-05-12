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
* Admin Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$adminView = new Admin_View($tpl);
$adminModel = new Admin();
// switch based on the action, NO default action here
$pageTitle = $option->pageTitle->action->{$registry->route['action']};
switch ($registry->route['action'])
{
	case 'login':
		// show the Login form
		$adminView->loginForm('login');
	break;	
	case 'logout':
		$dotAuth = Dot_Auth::getInstance();
		$dotAuth->clearIdentity('admin');
		header('location: '.$registry->configuration->website->params->url.'/' . $registry->route['module']);
		exit;
	break;	
	case 'authorize':
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'] && 
			array_key_exists('username', $_POST) || array_key_exists('password', $_POST))
		{	
			// else validate the authorization request parameters 
			$values = array('username' => 
								array('username' => $_POST['username']), 
							'password' => array('password' => $_POST['password'])
						  );
			$dotValidateUser = new Dot_Validate_User(array('who' => 'admin', 'action' => 'login', 'values' => $values));
			if($dotValidateUser->isValid())
			{
				$adminModel->authorizeLogin($dotValidateUser->getData());
			}
			else
			{
				$error = $dotValidateUser->getError();
				// login info are NOT VALID
				$txt = array();
				$field = array('username', 'password');
				foreach ($field as $v)
				{
					if(array_key_exists($v, $error))
					{
						 $txt[] = $error[$v];
					}
				}
				$registry->session->message['txt'] = $txt;
				$registry->session->message['type'] = 'error';
		
			}
		}
		else
		{
			$registry->session->message['txt'] = $option->warningMessage->userPermission;
			$registry->session->message['type'] = 'warning';
		}		
		header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/login');
		exit;		
	break;
	case 'account':
		//display my account form
		$data = $adminModel->getUserBy('id', $registry->session->admin->id);
		$adminView->details('account',$data);	
	break;
	case 'list':
		// list admin users
		$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
		$users = $adminModel->getUserList($page);		
		$adminView->listUser('list', $users, $page);	
	break;	
	case 'add':
		// display form and add new admin
		$data = $_POST;
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{		
			Dot_Kernel::checkUserToken();
			// POST values that will be validated				
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
									 ),
							'username' => 
								array('username' => $_POST['username']
									 ),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1',
											'isActive' => $_POST['isActive']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );			
			$dotValidateUser = new Dot_Validate_User(array('who' => 'admin', 'action' => 'add', 'values' => $values));
			if($dotValidateUser->isValid())
			{
				$data = $dotValidateUser->getData();
				// check if admin already exists by $field ('username','email')
				$checkBy = array('username', 'email');
				foreach ($checkBy as $field)
				{					
				   	$adminExists = $adminModel->getUserBy($field, $data[$field]);
					if(!empty($adminExists))
					{
						$error = ucfirst($field) . ' '. $data[$field] . $option->errorMessage->userExists;
					}
				}
				if(empty($error))
				{
					// no error - then add admin user
					$adminModel->addUser($data);				
					$registry->session->message['txt'] = $option->infoMessage->accountAdd;
					$registry->session->message['type'] = 'info';
					header('Location: '.$registry->configuration->website->params->url. '/' . $registry->route['module'] . '/' . $registry->route['controller']. '/list/');
					exit;					
				}	
			}
			$error = array_merge($error, $dotValidateUser->getError());
			$data = $dotValidateUser->getData();
			if(!empty($error))
			{						
				$registry->session->message['txt'] = $error;
				$registry->session->message['type'] = 'error';
			}
		}
		$adminView->details('add',$data);
	break;
	case 'update':
		// display form and update admin user
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{
			Dot_Kernel::checkUserToken();	
			// POST values that will be validated
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
							),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1'));
			if($_POST['password'] != '' || $_POST['password2'] !='' )
			{
				$values['password'] = array('password' => $_POST['password'],
											'password2' =>  $_POST['password2']
										   );
			}
			if(isset($_POST['isActive']))
			{
				$values['enum']['isActive'] =  $_POST['isActive'];
			}
			$dotValidateUser = new Dot_Validate_User(array('who' => 'admin', 'action' => 'update', 'values' => $values, 'userId' => $registry->request['id']));
			if($dotValidateUser->isValid())
			{
				$data = $dotValidateUser->getData();
				// no error - then update admin user
				$data['id'] = $registry->request['id'];
				$adminModel->updateUser($data);
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
		$data = $adminModel->getUserBy('id', $registry->request['id']);
		$adminView->setExtraBreadcrumb($data['username']);
		$pageTitle .= ' "' . $data['username'] . '"';
		$adminView->details('update',$data);
	break;
	case 'activate':
		// activate/deactivate admin user
		// this action is called via Ajax
		Dot_Kernel::checkUserToken();
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$values = array('enum' => array('0' => '0,1', 'isActive' => $isActive));		
		$dotValidateUser = new Dot_Validate_User(array('who' => 'admin', 'action' => 'activate', 'values' => $values));

		if($dotValidateUser->isValid())		
		{	
			$data = $dotValidateUser->getData();
			// no error - then change active value of admin user
			$adminModel->activateUser($id, $data['isActive']);
			$result = array(
				"success" => true,
				"id" => $id,
				"isActive" => intval($data["isActive"])
			);
		}
		else
		{
			$result = array("success" => false, "message" => "An error occured");
		}
		echo Zend_Json::encode($result);
		exit;
	break;
	case 'delete':			
		// display confirmation form and delete admin user
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{	
			Dot_Kernel::checkUserToken();
			if ('on' == $_POST['confirm'])
			{
				// delete admin user
				$adminModel->deleteUser($registry->request['id']);
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
		$data = $adminModel->getUserBy('id', $registry->request['id']);
		$adminView->setExtraBreadcrumb($data['username']);
		$pageTitle .= ' "' . $data['username'] . '"';
		// delete page confirmation
		$adminView->details('delete', $data);	
	break;
	case 'logins':
		// list user logins
		$id = (isset($registry->request['id'])) ? (int)$registry->request['id'] : 0;		
		$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
		$logins = $adminModel->getLogins($id, $page);
		$adminView->loginsUser('logins', $logins, $page);
	break;
}
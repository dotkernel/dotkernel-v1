<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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
$pageTitle = $option->pageTitle->action->{$requestAction};
switch ($requestAction)
{
	case 'login':
		// show the Login form
		$adminView->loginForm('login');
	break;	
	case 'logout':
		Dot_Auth::clearIdentity('admin');
		header('location: '.$config->website->params->url.'/' . $requestModule);
		exit;
	break;	
	case 'authorize':	
		// validate the authorization request parameters 
		$validate = $adminModel->validateLogin($_POST['username'], $_POST['password'], $_POST['send']);
		if(!empty($validate['login']) && empty($validate['error']))
		{
			// login info are VALID, we can see if is a valid user now 
			$user = $adminModel->checkLogin($validate['login']);
			if(!empty($user))
			{
				$session->admin = $user[0];
				//prepare data for register the login
				$dataLogin = array('ip' => Dot_Kernel::getUserIp(), 
							  'adminId' => $session->admin['id'], 
							  'username' => $session->admin['username'], 
							  'referer' => $_SERVER['HTTP_REFERER'],
							  'userAgent' => $_SERVER["HTTP_USER_AGENT"]);
				$adminModel->registerLogin($dataLogin);
				header('Location: '.$config->website->params->url.'/' . $requestModule );
				exit;
			}
			else
			{
				unset($session->admin);
				// check if account is inactive
				$adminTmp = $adminModel->getUserBy('username',$validate['login']['username']);
				(1 == $adminTmp['isActive']) ?
					$session->message['txt'] = $option->errorMessage->wrongCredentials:
					$session->message['txt'] = $option->errorMessage->inactiveAcount;
				$session->message['type'] = 'error';				
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/login');
				exit;				
			}
		}
		else
		{
			// login info are NOT VALID
			$session->message['txt'] = array($validate['error']['username'], $validate['error']['password']);
			$session->message['type'] = 'error';
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/login');
			exit;
		}			
	break;
	case 'account':
		//display my account form
		$data = $adminModel->getUserBy('id', $session->admin['id']);
		$adminView->details('update',$data);	
	break;
	case 'list':
		// list admin users
		$page = (isset($request['page'])) ? $request['page'] : 1;
		$users = $adminModel->getUserList($page);		
		$adminView->listUser('list', $users, $page);	
	break;	
	case 'add':
		// display form and add new admin
		$data = array();
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{		
			// POST values that will be validated				
			$values = array('username' => 
								array('username' => $_POST['username']
									 ),
							'details' => 
								array('firstName' => $_POST['firstName'],
									  'lastName' => $_POST['lastName']
									 ),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1',
											'isActive' => $_POST['isActive']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $adminModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{	
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
			}
			if(empty($error))
			{
				// no error - then add admin user
				$adminModel->addUser($data);				
				$session->message['txt'] = $option->infoMessage->accountAdd;
				$session->message['type'] = 'info';
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list/');
				exit;					
			}
			else
			{				
				$session->message['txt'] = $error;
				$session->message['type'] = 'error';
			}
		}
		$adminView->details('add',$data);		
	break;
	case 'update':
		// display form and update admin user
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{				
			// POST values that will be validated						
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
									 ),
							'email' => array('email' => $_POST['email']),
							'enum' => array('0' => '0,1',
											'isActive' => $_POST['isActive']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $adminModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];			
			if(empty($error))
			{
				// no error - then update admin user
				$data['id'] = $request['id'];				
				$adminModel->updateUser($data);
				$session->message['txt'] = $option->infoMessage->accountUpdate;
				$session->message['type'] = 'info';
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list/');
				exit;				
			}
			else
			{
				$session->message['txt'] = $error;
				$session->message['type'] = 'error';
			}
		}
		$data = $adminModel->getUserBy('id', $request['id']);
		$adminView->details('update',$data);	
	break;
	case 'activate':
		// this action is called from ajax request dojo.xhrPost()
		// activate/inactivate admin user
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		$values = array('enum' => array('0' => '0,1', 'isActive' => $isActive));
		$valid = $adminModel->validateUser($values);
		if(empty($valid['error']))
		{	
			// no error - then change active value of admin user
			$adminModel->activateUser($id, $valid['data']['isActive']);		
		}
		else
		{
			$session->message['txt'] = $option->errorMessage->trickUserError;
			$session->message['type'] = 'error';
		}
		$users = $adminModel->getUserList($page);
		$session->useAjaxView = true;		
		$adminView->listUser('list', $users, $page, true);
	break;
	case 'delete':
		// display confirmation form and delete admin user
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{	
			if (1 == $_POST['delete'])
			{
				// delete admin user
				$adminModel->deleteUser($request['id']);
				$session->message['txt'] = $option->infoMessage->accountDelete;
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $option->infoMessage->noAccountDelete;
				$session->message['type'] = 'info';
			}
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list/');
			exit;				
		}
		$data = $adminModel->getUserBy('id', $request['id']);
		// delete page confirmation
		$adminView->details('delete', $data);	
	break;
	case 'logins':
		// list user logins
		$id = (isset($request['id'])) ? (int)$request['id'] : 0;		
		$page = (isset($request['page'])) ? $request['page'] : 1;
		$logins = $adminModel->getLogins($id, $page);
		$adminView->loginsUser('logins', $logins, $page);
	break;
}
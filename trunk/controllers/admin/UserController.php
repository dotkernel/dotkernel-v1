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
* System Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

// instantiate classes related to User module: model & view
$userModel = new User(); 
$userView = new User_View($tpl);
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$requestAction};
// switch based on the action, don't forget the default action
switch ($requestAction)
{
	default:
	case 'list':
		// list users
		$page = (isset($request['page'])) ? $request['page'] : 1;
		$users = $userModel->getUserList($page);		
		$userView->listUser('list', $users, $page);		
	break;
	case 'add':		
		// display form and add new user
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
			$valid = $userModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{	
				// check if user already exists by $field ('username','email')
				$checkBy = array('username', 'email');
				foreach ($checkBy as $field)
				{					
				   	$userExists = $userModel->getUserBy($field, $data[$field]);
					if(!empty($userExists))
					{
						$error = ucfirst($field) . ' '. $data[$field] . $option->errorMessage->userExists;
					}
				}	
			}
			if(empty($error))
			{
				// no error - then add user
				$userModel->addUser($data);				
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
		$userView->details('add',$data);		
	break;
	case 'update':
		// display form and update user
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
			$valid = $userModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];			
			if(empty($error))
			{
				// no error - then update user
				$data['id'] = $request['id'];				
				$userModel->updateUser($data);
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
		$data = $userModel->getUserBy('id', $request['id']);
		$userView->details('update',$data);	
	break;
	case 'activate':
		// activate/inactivate user account
		// this action is called from ajax request dojo.xhrPost()
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		$values = array('enum' => array('0' => '0,1', 'isActive' => $isActive));
		$valid = $userModel->validateUser($values);
		if(empty($valid['error']))
		{	
			// no error - then change active value of user
			$userModel->activateUser($id, $valid['data']['isActive']);		
		}
		else
		{
			$session->message['txt'] = $option->errorMessage->trickUserError;
			$session->message['type'] = 'error';
		}
		$users = $userModel->getUserList($page);
		$session->useAjaxView = true;		
		$userView->listUser('list', $users, $page, true);
	break;
	case 'delete':
		// display confirmation form and delete user account
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{	
			if (1 == $_POST['delete'])
			{
				// delete user
				$userModel->deleteUser($request['id']);
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
		$data = $userModel->getUserBy('id', $request['id']);
		// delete page confirmation
		$userView->details('delete', $data);	
	break;
	case 'send-password':
		// send an email with the password to the selected user
		$data = array();
		$error = array();
		if ($request['id'] > 0)
		{
			// send user password 
			$userModel->sendPassword($request['id']);				
		}
		else
		{
			$session->message['txt'] = $option->errorMessage->emailNotSent;
			$session->message['type'] = 'error';
		}
		header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list/');
		exit;		
	break;
}
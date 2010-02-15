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
* User Controller
* @author     DotKernel Team <team@dotkernel.com>
*/
// All actions MUST return this variable
$pageTitle = '';

// instantiate  AuthUser object
$adminUser = new Admin_User(); 
$userView = new User_View($tpl);
// switch based on the action, NO default action here
switch ($requestAction)
{
	case 'login':
		// Show the Login form
		$pageTitle = 'Admin Login User';	
		$userView->loginForm('login');
	break;	
	case 'logout':
		$adminUser->logout();
		header('location: '.$config->website->params->url.'/' . $requestModule);
		exit;
	break;	
	case 'auth':	
		// validate the authorization request paramethers 
		$validate = Dot_AuthorizeUser::validateLogin($_POST['username'], $_POST['password'], $_POST['send']);
		if(!empty($validate['login']) && empty($validate['error']))
		{
			// login info are VALID, we can see if is a valid user now 
			$user = $adminUser->checkLogin($validate['login']);
			if(!empty($user))
			{
				$_SESSION['kernel']['admin'] = $user[0];
				header('location: '.$config->website->params->url.'/' . $requestModule );
				exit;
			}
			else
			{
				unset($_SESSION['kernel']['admin']);
				$_SESSION['kernel']['login_user'] = 'Wrong Login Credentials';
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/login');
				exit;				
			}
		}
		else
		{
			// login info are NOT VALID
			$_SESSION['kernel']['login_user'] = $validate['error']['username'] . ' <br> '. $validate['error']['password'];
			header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/login');
			exit;
		}			
	break;
	case 'account':
		$pageTitle = 'My Admin User Account';
		$data = $adminUser->getUserInfo($_SESSION['kernel']['admin']['id']);
		$userView->details('update',$data);	
	break;
	case 'list':
		$pageTitle = 'List Admin Users';
		$page = (isset($request['page'])) ? $request['page'] : 1;
		$users = $adminUser->getUserList($page);		
		$userView->listUser('list', $users,$page);	
	break;	
	case 'add':
		$data = array();
		$error = array();
		$pageTitle = 'Add New Admin User';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{						
			$values = array('username'=>$_POST['username'],
						'firstname'=>$_POST['firstname'],
						'lastname'=>$_POST['lastname']
						);
			$valid = $adminUser->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{
				//add admin user
				$adminUser->addUser($data);
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list');
				exit;	
				
			}
			elseif(array_key_exists('password', $data))
			{ 
				// do not display password in the add form
				unset($data['password']);
			}
		}
		$userView->details('add',$data,$error);		
	break;
	case 'update':
		$error = array();
		$pageTitle = 'Update Admin User';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{						
			$values = array('firstname'=>$_POST['firstname'],
						'lastname'=>$_POST['lastname']
						);
			$valid = $adminUser->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{
				$data['id'] = $request['id'];
				//add admin user
				$adminUser->updateUser($data);
				header('Location: '.$config->website->params->url. '/' . $requestModule . '/' . $requestController. '/list');
				exit;				
			}
		}
		$data = $adminUser->getUserInfo($request['id']);
		$userView->details('update',$data,$error);	
	break;
}



<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	default:
		// default action is login
		$pageTitle = $option->pageTitle->action->login;
	case 'login':
		if(!isset($session->user))
		{
			// display Login form
			$userView->loginForm('login');
		}
		else
		{			
			header('Location: '.$config->website->params->url.'/user/account');
			exit;
		}
	break;
	case 'authorize':
		// authorize user login 
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'] && 
			array_key_exists('username', $_POST) || array_key_exists('password', $_POST))
		{	
			// validate the authorization request parameters 
			$validate = $userModel->validateLogin($_POST['username'], $_POST['password'], $_POST['send']);
			$userModel->authorizeLogin($validate);
		}
		else
		{
			$session->message['txt'] = $option->warningMessage->userPermission;
			$session->message['type'] = 'warning';
		}
		header('Location: '.$config->website->params->url. '/' . $requestController. '/login');
		exit;			
	break;
	case 'account':
		// display My Account page, if user is logged in 
		Dot_Auth::checkIdentity();
		$data = array();
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{				
			// POST values that will be validated				
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
									 ),
							'email' => array('email' => $_POST['email']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $userModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			$data['id'] = $request['id'];		
			if(empty($error))
			{				
				// no error - then update user
				$userModel->updateUser($data);
				$session->message['txt'] = $option->infoMessage->update;
				$session->message['type'] = 'info';			
			}
			else
			{
				$session->message['txt'] = $error;
				$session->message['type'] = 'error';
			}			
			$dataTmp = $userModel->getUserInfo($session->user['id']);
			$data['username'] = $dataTmp['username'];
		}
		else
		{			
			$data = $userModel->getUserInfo($session->user['id']);
		}
		$userView->details('update',$data);	
	break;
	case 'register':
		// display signup form and allow user to register 
		$data = array();
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{		
			// POST values that will be validated				
			$values = array('details' => 
								array('firstName'=>$_POST['firstName'],
									  'lastName'=>$_POST['lastName']
									 ),
							'username' => array('username'=>$_POST['username']),
							'email' => array('email' => $_POST['email']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $userModel->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(!isset($_POST['recaptcha_response_field']) || strlen($_POST['recaptcha_response_field']) == 0)
			{
				$error['Secure Image'] = $option->errorMessage->captcha;
			}
			else
			{
				// validate secure image code
				try
				{
					$result = $userView->getRecaptcha()->verify($_POST['recaptcha_challenge_field'],$_POST['recaptcha_response_field']);
					if (!$result->isValid()) 
					{
						$error['Secure Image'] = $option->errorMessage->captcha;
					}
				}
				catch(Zend_Exception $e)
				{
					$error['Secure Image'] = $option->errorMessage->captcha . ' ' . $e->getMessage();
				}
			}	
			if(empty($error))
			{	
				//check if user already exists by $field ('username','email')
				$checkBy = array('username','email');
				foreach ($checkBy as $field)
				{					
				   	$userExists = $userModel->getUserBy($field, $data[$field]);
					if(!empty($userExists))
					{
						$error[$field] = ucfirst($field).$option->errorMessage->userExists;
					}
				}	
			}
			if(empty($error))
			{				
			   	// no error - then add user
				$userModel->addUser($data);
				$session->message['txt'] = $option->infoMessage->add;
				$session->message['type'] = 'info';
				$validate = $userModel->validateLogin($data['username'], $data['password'], 'on');
				if(!empty($validate['login']) && empty($validate['error']))
				{
					// login info are VALID, we can see if is a valid user now 
					$user = $userModel->checkLogin($validate['login']);
					if(!empty($user))
					{
						$session->user = $user;
						$data = array();
						$error = array();
					}
					else
					{
						//this else case should never be reach
						unset($session->user);
						$error['Error Login'] = $option->errorMessage->login;
					}
				}
			}
			else
			{	
				if(array_key_exists('password', $data))
				{ 
					// do not display password in the add form
					unset($data['password']);				
				}							
			}
			// add action and validation are made with ajax - dojo.xhrPost, so return json string  
			echo Zend_Json::encode(array('data'=>$data, 'error'=>$error));
			// return $data and $error as json
			exit;			
		}
		$userView->details('add',$data);
	break;
	case 'forgot-password':
		// send an emai with the forgotten password
		$data = array();
		$error = array();
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{				
			$valid = $userModel->validateEmail($_POST['email']);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{	
				// no error - then send password
				$userModel->forgotPassword($data['email']);						
			}
			else
			{
				$session->message['txt'] = $error;
				$session->message['type'] = 'error';
			}			
		}
		$userView->details('forgot_password',$data);		
	break;
	case 'logout':
		Dot_Auth::clearIdentity('user');
		header('location: '.$config->website->params->url);
		exit;
	break;	
}
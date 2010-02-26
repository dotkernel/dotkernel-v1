<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Frontend
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
$frontendUser = new Frontend_User(); 
$userView = new User_View($tpl, $settings);
// switch based on the action, NO default action here
switch ($requestAction)
{
	case 'login':
		// Show the Login form
		$pageTitle = 'Login User';	
		$userView->loginForm('login');
	break;
	case 'auth':
		// validate the authorization request paramethers 
		$validate = Dot_AuthorizeUser::validateLogin($_POST['username'], $_POST['password'], $_POST['send']);
		if(!empty($validate['login']) && empty($validate['error']))
		{
			// login info are VALID, we can see if is a valid user now 
			$user = $frontendUser->checkLogin($validate['login']);
			if(!empty($user))
			{
				$session->user = $user[0];
				header('location: '.$config->website->params->url.'/user/account');
				exit;
			}
			else
			{
				unset($session->user);
				$session->loginUserError = 'Wrong Login Credentials';
				header('Location: '.$config->website->params->url.'/user/login');
				exit;				
			}
		}
		else
		{
			// login info are NOT VALID
			$session->loginUserError = $validate['error']['username'] . ' <br> '. $validate['error']['password'];
			header('Location: '.$config->website->params->url.'/user/login');
			exit;
		}			
	break;
	case 'account':
		// Show My Account Page, if he is logged in 
    	Dot_AuthorizeUser::checkPermissions($config);
		$data = array();
		$error = array();
		$pageTitle = 'User Account';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{						
			$values = array('alpha' => 
								array('firstname'=>$_POST['firstname'],
									  'lastname'=>$_POST['lastname']
									 ),
							'email' => array('email' => $_POST['email']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $frontendUser->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			$data['id'] = $request['id'];		
			if(empty($error))
			{				
				//update user
				$frontendUser->update($data);
				
			}
			elseif(array_key_exists('password', $data))
			{ 
				// do not display password in the add form
				unset($data['password']);
			}
			$dataTmp = $frontendUser->getUserInfo($session->user['id']);
			$data['username'] = $dataTmp['username'];
		}
		else
		{			
			$data = $frontendUser->getUserInfo($session->user['id']);
		}
		$userView->details('update',$data,$error);	
	break;
	case 'register':
		$data = array();
		$error = array();
		$pageTitle = 'User Register';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{		
			$values = array('alpha' => 
								array('username'=>$_POST['username'],
									  'firstname'=>$_POST['firstname'],
									  'lastname'=>$_POST['lastname']
									 ),
							'email' => array('email' => $_POST['email']),
							'password' => array('password' => $_POST['password'],
												'password2' =>  $_POST['password2']
											   )
						  );
			$valid = $frontendUser->validateUser($values);
			$data = $valid['data'];
			$error = $valid['error'];
			if(strlen($_POST['recaptcha_response_field']) == 0)
			{
				$error['Secure Image'] = 'Incorrect. Try again.';
			}
			else
			{
				// validate secure image code
				$result = $userView->getRecaptcha()->verify($_POST['recaptcha_challenge_field'],$_POST['recaptcha_response_field']);				
				if (!$result->isValid()) 
				{
					$error['Secure Image'] = 'Incorrect. Try again. ';
				}
			}	
			if(empty($error))
			{	
				//check if user already exists by $field ('username','email')
				$checkBy = array('username','email');
				foreach ($checkBy as $field)
				{					
				   	$userExists = $frontendUser->getUserBy($field, $data[$field]);
					if(!empty($userExists))
					{
						$error[$field] = ucfirst($field).' already exists !';
					}
				}	
			}
			if(empty($error))
			{
				
			   	//add admin user
				$frontendUser->add($data);
				$validate = Dot_AuthorizeUser::validateLogin($data['username'], $data['password'], 'on');
				if(!empty($validate['login']) && empty($validate['error']))
				{
					// login info are VALID, we can see if is a valid user now 
					$user = $frontendUser->checkLogin($validate['login']);
					if(!empty($user))
					{
						$session->user = $user[0];
						$data = array();
						$error = array();
					}
					else
					{
						unset($session->user);
						$error['Error Login'] = 'Wrong Login Credentials';
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
			//return $data and $error as json
			echo Zend_Json::encode(array('data'=>$data, 'error'=>$error));
			exit;			
		}
		$userView->details('add',$data,$error);
	break;
	case 'forgot-password':
		$data = array();
		$error = array();
		$pageTitle = 'Forgot your password?';
		if(array_key_exists('send', $_POST) && 'on' == $_POST['send'])
		{				
			$valid = $frontendUser->validateEmail($_POST['email']);
			$data = $valid['data'];
			$error = $valid['error'];
			if(empty($error))
			{	
				 // re-send password
				$error = $frontendUser->forgotPassword($data['email']);						
			}
		}
		$userView->details('forgot_password',$data,$error);		
	break;
	case 'logout':
		$frontendUser->logout();
		header('location: '.$config->website->params->url);
		exit;
	break;			
}
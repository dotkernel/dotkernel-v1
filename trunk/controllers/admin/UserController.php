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
 * User Controller
 * @author     DotKernel Team <team@dotkernel.com>
 */

// instantiate classes related to User module: model & view
$userModel = new User();
$userView = new User_View($tpl);
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$registry->requestAction};
switch ($registry->requestAction)
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
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'add', 'values' => $values));
			if($dotValidateUser->isValid())
			{
				// no error - then add user
				$userModel->addUser($dotValidateUser->getData());
				$registry->session->message['txt'] = $option->infoMessage->accountAdd;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
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
			// POST values that will be validated
			$values = array('details' =>
																	array('firstName'=>$_POST['firstName'],
																				'lastName'=>$_POST['lastName']),
																				'username' => array('username' => $_POST['username']),
																				'email' => array('email' => $_POST['email']),
																				'enum' => array('0' => '0,1', 'isActive' => $_POST['isActive'])
											);
			
			// Only if a new password is provided we will update the password field
			if($_POST['password'] != '' || $_POST['password2'] !='' )
			{
				$values['password'] = array('password' => $_POST['password'], 'password2' =>  $_POST['password2']);
			}
			
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'update', 'values' => $values, 'userId' => $registry->request['id']));
			if($dotValidateUser->isValid())
			{
				// no error - then update user
				$data = $dotValidateUser->getData();
				$data['id'] = $registry->request['id'];
				$userModel->updateUser($data);
				$registry->session->message['txt'] = $option->infoMessage->accountUpdate;
				$registry->session->message['type'] = 'info';
				header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
				exit;
			}
			else
			{
				$registry->session->message['txt'] = $dotValidateUser->getError();
				$registry->session->message['type'] = 'error';
			}
		}
		$data = $userModel->getUserBy('id', $registry->request['id']);
		$userView->setExtraBreadcrumb($data['username']);
		$pageTitle .= ' "' . $data['username'] . '"';
		$userView->details('update',$data);
	break;
	case 'activate':
		// activate/deactivate user account
		// this action is called via Ajax
		if(!Dot_Auth::checkUserToken($userToken)) // Don't do anything unless token is valid
		{
			echo Zend_Json::encode(array("success" => false, "message" => "An error occured, please try again."));
			exit;
		}
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isActive = (isset($_POST['isActive'])) ? $_POST['isActive'] : 0;
		$values = array('enum' => array('0' => '0,1', 'isActive' => $isActive));
		$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'activate', 'values' => $values));

		if($dotValidateUser->isValid())
		{
			$data = $dotValidateUser->getData();
			// no error - then change active value of user
			$userModel->activateUser($id, $data['isActive']);
			$result = array(
				"success" => true,
				"id" => $id,
				"isActive" => intval($data["isActive"])
			);
		}
		else
		{
			$result = array("success" => false, "message" => "An error occured, please try again.");
		}
		echo Zend_Json::encode($result);
		exit;
	break;
	case 'delete':
		// display confirmation form and delete user account
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
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
			exit;
		}
		if (!$registry->request['id'])
		{
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
			exit;
		}
		$data = $userModel->getUserBy('id', $registry->request['id']);
		$userView->setExtraBreadcrumb($data['username']);
		$pageTitle .= ' "' . $data['username'] . '"';
		// delete page confirmation
		$userView->details('delete', $data);
	break;
	case 'send-password':
		// send an email with the password to the selected user
		$data = array();
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			if ('on' == $_POST['confirm'])
			{
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
			}
			else
			{
				$registry->session->message['txt'] = $option->infoMessage->passwordNotSent;
				$registry->session->message['type'] = 'warning';
			}
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
			exit;
		}
		if (!$registry->request['id'])
		{
			header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestModule . '/' . $registry->requestController. '/list/');
			exit;
		}
		$data = $userModel->getUserBy('id', $registry->request['id']);
		$userView->setExtraBreadcrumb($data['username']);
		$pageTitle .= ' "' . $data['username'] . '"';
		$userView->details('sendPassword', $data);
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
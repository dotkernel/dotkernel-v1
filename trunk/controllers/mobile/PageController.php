<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Mobile
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Page Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$pageView = new Page_View($tpl);
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$registry->requestAction};
switch ($registry->requestAction)
{
	default:
		// default action is home
		$pageTitle = $option->pageTitle->action->home;
	case 'home':
		// call showPage method to view the home page
		$pageView->showPage('home');
		if($_SERVER['REQUEST_METHOD'] === "POST" && 
			array_key_exists('phone', $_POST) && array_key_exists('phone', $_POST) &&
			array_key_exists('email', $_POST) && array_key_exists('message', $_POST))
		{	
			// validate the response
			$values = array('email' => array('email' => $_POST['email']), 
							'details' => array('message' => $_POST['message'])
						  );
			//if phone is completed, validate phone to be from US
			if($_POST['phone'] != '')
			{
				$values['phone'] = array('phone' => $_POST['phone']);
			}
			$dotValidateUser = new Dot_Validate_User(array('who' => 'mobile', 'action' => 'form', 'values' => $values));
			if($dotValidateUser->isValid())
			{ 
				//if valid, send a mail
				$data = $dotValidateUser->getData();
				$dotEmail = new Dot_Email();
				$dotEmail->addTo($settings->siteEmail);
				$dotEmail->setSubject($registry->seo->siteName . ' - ' . $option->contactForm->subject);
				$msg = str_replace(array('%EMAIL%', '%PHONE%','%MESSAGE%', '%DATE%', '%IP%', '%USERAGENT%'), 
								   array($data['email'], isset($data['phone'])? $data['phone'] : '' , $data['message'], Dot_Kernel::timeFormat('now'), Dot_Kernel::getUserIp(), $_SERVER['HTTP_USER_AGENT']), 
					              $option->contactForm->message);
				$dotEmail->setBodyText($msg);
				$dotEmail->send();
				/** If you want to redirect to a link, 
				 *  uncomment the 2 lines below to display a message
				 */ 

				$tpl->setVar('ERROR_MESSAGE', $option->contactForm->mailSent);
			}
			else
			{
				$session->message['txt'] = $dotValidateUser->getError();
				$session->message['type'] = 'error';
				$pageView->showPage('home', $dotValidateUser->getData());
			}
		}
	break;
}
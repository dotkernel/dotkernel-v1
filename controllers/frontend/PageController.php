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
* Page Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

// All actions MUST set  the variable  $pageTitle

$pageView = new Page_View($tpl);
// switch based on the action, don't forget the default action
switch ($requestAction)
{
	default:
	case 'home';
		$pageTitle = 'Home Page';
		// a call to specific view function
		// like: viewControllerAction() , a.k.a viewPageHome()
		// call to view function
		$pageView->showPage('home');
	break;
	case 'about':
		$pageTitle = 'About Us';
		$pageView->showPage($requestAction);
	break;
	case 'who-we-are':
		$pageTitle = 'Who We Are';
		$pageView->showPage($requestAction);
	break;
	case 'outbound-links':
		$pageTitle = 'Outbound Links';
		$pageView->showPage($requestAction);
	break;
}
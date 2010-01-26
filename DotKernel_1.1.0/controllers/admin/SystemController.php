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
* System Controller
* @author     DotKernel Team <team@dotkernel.com>
*/
// All actions MUST return this variable
$pageTitle = '';

// instantiate  AuthUser object
$systemView = new System_View($tpl);
// switch based on the action, NO default action here
switch ($requestAction)
{
	case 'dashboard':
		$pageTitle = 'Dashboard';		
		$systemView->dashboard('dashboard');
	break;
	case 'phpinfo':
		phpinfo();
		exit;
	break;	
}



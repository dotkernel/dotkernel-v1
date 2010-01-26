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
* Frontend Module Controller
* Is doing all the job for specific frontend control stuff
* @author     DotKernel Team <team@dotkernel.com>
*/ 

// set Module and Action default values
$requestController = isset($requestController) && $requestController !='Index' ? $requestController : 'System';
$requestAction     = isset($requestAction) && $requestAction !=''         ? $requestAction     : 'dashboard';
// check admin permission
$authorizeUser = new Dot_AuthorizeUser();
if(!$authorizeUser->isLogin('admin') && $requestAction != 'auth')
{
	$requestController = 'User';
	$requestAction = 'login';
}

// Start the template object, empty for the moment 
require(DOTKERNEL_PATH . '/' . $requestModule . '/' . 'View.php');	
$tpl = View::getInstance(TEMPLATES_PATH . '/' . $requestModule);
$tpl->init($requestModule, $requestController, $requestAction);


// Assign Index Template file
$tpl->setViewFile();

// Set paths in templates
$tpl->setViewPaths($config);

/** 
 * each Controller  must load its own specific models and views
*/
Dot_Settings :: loadControllerFiles($requestModule);

/**
*  From this point , the control is taken by the Action specific controller
*  call the Action specific file, but check first if exists 
*/
$actionControllerPath = CONTROLLERS_PATH . '/' . $requestModule . '/' . $requestController . 'Controller.php';
!file_exists($actionControllerPath) ?  $dotKernel->pageNotFound() :  require($actionControllerPath);

//Set menus
$tpl->setViewMenu($config);

//Set info bar
$tpl->setInfoBar();

//Set  HTML head structure  tags 
$tpl->setViewTitle($settings, $pageTitle);

// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');
// show debug info ONLY of we are in development mode
if(ini_get('display_errors') == 1)
{
	$debug = new Dot_Debug($db, $tpl);
	$debug->startTimer = $startTime;
	$debug->show();
}

//parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');

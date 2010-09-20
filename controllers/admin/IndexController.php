<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Admin Module - Index Controller
* Is doing all the job for specific admin control stuff
* @author     DotKernel Team <team@dotkernel.com>
*/ 

// set Module and Action default values
$defaultController = $resource->route->controller->$requestModule;
$requestController = isset($requestController) && $requestController !='Index' ? $requestController : $defaultController;
$defaultAction = $resource->route->action->$requestModule->$requestController;
$requestAction     = isset($requestAction) && $requestAction !=''         ? $requestAction     : $defaultAction;

$route['controller'] = $requestController;
$route['action'] = $requestAction;
$registry->route = $route;
// check admin permission
if($requestAction != 'login' && $requestAction != 'authorize')
{
	Dot_Auth::checkIdentity('admin');
}

// start the template object, empty for the moment 
require(DOTKERNEL_PATH . '/' . $requestModule . '/' . 'View.php');	
$tpl = View::getInstance(TEMPLATES_PATH . '/' . $requestModule);
$tpl->init();

// assign Index Template file
$tpl->setViewFile();

// set paths in templates
$tpl->setViewPaths();

/** 
 * each Controller  must load its own specific models and views
*/
Dot_Settings :: loadControllerFiles($requestModule);

/**
 * Load options(specific configuration file for current dot) file
 * @todo linking N dots together
 */
$option = Dot_Settings::getOptionVariables($requestModule,$requestController);
$registry->option = $option;

/**
 * Start the variable for Page Title, this will be used as H1 tag too 
*/
$pageTitle = 'Overwrite Me Please !';

/**
*  From this point , the control is taken by the Action specific controller
*  call the Action specific file, but check first if exists 
*/
$actionControllerPath = CONTROLLERS_PATH . '/' . $requestModule . '/' . $requestController . 'Controller.php';
!file_exists($actionControllerPath) ?  $dotKernel->pageNotFound() :  require($actionControllerPath);

// set menus
$tpl->setViewMenu($config);

// set info bar
$tpl->setInfoBar();

// set SEO html tags from dots/seo.xml file
$tpl->setSeoValues($pageTitle);

// dispaly message (error, warning, info)	
$tpl->displayMessage();

// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');

// show debugbar 
if(TRUE == $config->settings->admin->debugbar &&
   ($requestController!= 'admin' && $requestAction!='login'))
{
	$debug = new Dot_Debug($db, $tpl);
	$debug->startTimer = $startTime;
	$debug->show();
}
// parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');

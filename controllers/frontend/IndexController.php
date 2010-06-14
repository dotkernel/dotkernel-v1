<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
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
$defaultController = $resource->route->controller->$requestModule;
$requestController = isset($requestController) && $requestController !='Index' ? $requestController : $defaultController;
$defaultAction = $resource->route->action->$requestModule->$requestController;
$requestAction     = isset($requestAction) && $requestAction !=''         ? $requestAction     : $defaultAction;
// start the template object, empty for the moment 
require(DOTKERNEL_PATH . '/' . $requestModule . '/' . 'View.php');	
$tpl = View::getInstance(TEMPLATES_PATH . '/' . $requestModule);
$tpl->init($requestModule, $requestController, $requestAction);

// assign Index Template file
$tpl->setViewFile();

// set paths in templates
$tpl->setViewPaths($config);

// display login box
$tpl->setLoginBox();

/** 
 * each Controller  must load its own specific models and views
 */
Dot_Settings :: loadControllerFiles($requestModule);

/**
 * Load option(specific configuration file for current dot) file
 * @TODO linking N dots together
 */
$option = Dot_Settings::getOptionVariables($requestModule,$requestController);
$registry->option = $option;

/**
 * Start the variable for Page Title, this will be used as H1 tag too 
 */
$pageTitle = 'Overwrite Me Please !';

/**
 * From this point , the control is taken by the Action specific controller
 * call the Action specific file, but check first if exists 
 */
$actionControllerPath = CONTROLLERS_PATH . '/' . $requestModule . '/' . $requestController . 'Controller.php';
!file_exists($actionControllerPath) ?  $dotKernel->pageNotFound() :  require($actionControllerPath);
	
// set menus
$tpl->setViewMenu($config);

// set SEO html tags from dots/seo.xml file
$tpl->setSeoValues($pageTitle);

// dispaly message (error, warning, info)	
$tpl->displayMessage();

// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');

// show debugbar 
if(TRUE == $config->settings->frontend->debugbar)
{
	$debug = new Dot_Debug($db, $tpl, $config);
	$debug->startTimer = $startTime;
	$debug->show();
}
// parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');

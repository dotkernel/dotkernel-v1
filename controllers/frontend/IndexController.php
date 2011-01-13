<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
 * Frontend Module - Index Controller
 * Is doing all the job for specific frontend control stuff
 * @author     DotKernel Team <team@dotkernel.com>
 */ 

//is mobile and session->mobileHit is not set, register it and redirect
if('mobile' == Dot_Kernel::getDevice()->getType() && !isset($session->mobileHit))
{
	// register mobile hits, store in session if hit was register
	$dotMobile = new Dot_Mobile();
	$dotMobile->registerHit();
	$session->mobileHit = TRUE;
	//redirect to mobile controller
	header('location: '.$config->website->params->url.'/mobile');
	exit;
}

// start the template object, empty for the moment 
require(DOTKERNEL_PATH . '/' . $registry->route['module'] . '/' . 'View.php');	
$tpl = View::getInstance(TEMPLATES_PATH . '/' . $registry->route['module']);
$tpl->init();

// assign Index Template file
$tpl->setViewFile();

// set paths in templates
$tpl->setViewPaths();

// display login box
$tpl->setLoginBox();

/** 
 * each Controller  must load its own specific models and views
 */
Dot_Settings :: loadControllerFiles($registry->route['module']);

/**
 * Load option(specific configuration file for current dot) file
 * @todo linking N dots together
 */
$option = Dot_Settings::getOptionVariables($registry->route['module'],$registry->route['controller']);
$registry->option = $option;

/**
 * Start the variable for Page Title, this will be used as H1 tag too 
 */
$pageTitle = 'Overwrite Me Please !';

/**
 * From this point , the control is taken by the Action specific controller
 * call the Action specific file, but check first if exists 
 */
$actionControllerPath = CONTROLLERS_PATH . '/' . $registry->route['module'] . '/' . ucfirst($registry->route['controller']) . 'Controller.php';
!file_exists($actionControllerPath) ?  $dotKernel->pageNotFound() :  require($actionControllerPath);
	
// set menus
$tpl->setViewMenu();

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

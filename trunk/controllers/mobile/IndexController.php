<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Mobile
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Mobile Module - Index Controller
* Is doing all the job for specific Mobile control stuff 
* @author     DotKernel Team <team@dotkernel.com>
*/

// initialize the session
// if you don't use the session object in this module, feel free to remove this line
Dot_Session::start();

// start the template object, empty for the moment 
require(DOTKERNEL_PATH . '/' . $registry->requestModule . '/' . 'View.php');
$tpl = View::getInstance(TEMPLATES_PATH . '/' . $registry->requestModule);
$tpl->init();

// assign Index Template file
$tpl->setViewFile();

// set paths in templates
$tpl->setViewPaths();
/** 
 * each Controller  must load its own specific models and views
 */
Dot_Settings :: loadControllerFiles($registry->requestModule);

/**
 * Load option(specific configuration file for current dot file
 */
$option = Dot_Settings::getOptionVariables($registry->requestModule, $registry->requestControllerProcessed);
$registry->option = $option;

/**
 * Start the variable for Page Title, this will be used as H1 tag too 
 */
$pageTitle = 'Overwrite Me Please !';

/**
 * From this point , the control is taken by the Action specific controller
 * call the Action specific file, but check first if exists 
 */
$actionControllerPath = CONTROLLERS_PATH . '/' . $registry->requestModule . '/' . $registry->requestControllerProcessed . 'Controller.php';
!file_exists($actionControllerPath) ?  Dot_Route::pageNotFound() : require($actionControllerPath);

// set SEO html tags from dots/seo.xml file
$tpl->setSeoValues($pageTitle);

// dispaly message (error, warning, info)	
$tpl->displayMessage();
	
// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');

// parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');

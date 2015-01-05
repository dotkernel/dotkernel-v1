<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Admin Module - Index Controller
* Is doing all the job for specific admin control stuff
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
 * Load option(specific configuration file for current dot) file
 */
$option = Dot_Settings::getOptionVariables($registry->requestModule, $registry->requestControllerProcessed);
$registry->option = $option;

/**
 * Start the variable for Page Title, this will be used as H1 tag too 
 */
$pageTitle = 'Overwrite Me Please !';


/**
 * From rev 833
 * DotAuth::checkUserToken() will be given at least one parameter
 * $userToken - mandatory
 * $userType - optional - by default 'admin'
 *
 * To simplify it, we will put the variable $userToken
 * If we do not have the token, it will be marked with NULL
 *
 * NULL - user does not have a token
 * ''   - user have an empty token
 * Any other (string) value - the token
 *
 * See Dot_Auth::checkUserToken()
 */
$userToken = (isset($_POST['userToken'])) ? $_POST['userToken'] : null;

/**
 * From this point , the control is taken by the Action specific controller
 * call the Action specific file, but check first if exists 
 */
$actionControllerPath = CONTROLLERS_PATH . '/' . $registry->requestModule . '/' . $registry->requestControllerProcessed . 'Controller.php';

if(file_exists($actionControllerPath))
{
	$dotAuth = Dot_Auth::getInstance();
	$dotAuth->checkIdentity('admin');
	require($actionControllerPath);
}
else
{
	Dot_Route::pageNotFound('admin');
}

// set menus
$tpl->setViewMenu();

// set info bar
$tpl->setInfoBar();

// set SEO html tags from dots/seo.xml file
$tpl->setSeoValues($pageTitle);

// dispaly message (error, warning, info)	
$tpl->displayMessage();

// display widgets
$tpl->displayWidgets($option->widgets->content);

// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');

// show debugbar, only for logged in admins
if (isset($_SESSION['admin']['admin']))
{
	$debug = new Dot_Debug($tpl);
	$debug->show();
}

// parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');
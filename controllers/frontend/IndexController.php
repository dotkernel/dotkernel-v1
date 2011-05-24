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

// initialize the session
// if you don't use the session object in this module, feel free to remove this line
Dot_Session::start();

/**
 *  Example of usage for WURFL API integration. If wurfl  module is active, you can redirect to /mobile controller 
 */
if($registry->configuration->resources->useragent->wurflapi->active)
{
	$deviceInfo = Dot_UserAgent :: getDeviceInfo($_SERVER["HTTP_USER_AGENT"]); 
	if( (0 < count((array)$deviceInfo)) && $deviceInfo->isMobile)
	{
		/**
 		* Example of usage of Statistic class. We may want to record every site visits, in order to find new mobile device
 		* that are not listed in WURFL xml file. Record in session the visitId for later usage.
 		*/
		if(!$registry->session->visitId)
		{
			$registry->session->visitId = Dot_Statistic::registerVisit();
		}
		
		// if the Statistic module is integrate, record the deviceInfo too, and record TRUE in $session->mobile 
		if(!$registry->session->mobile)
		{
			$registry->session->mobile = Dot_Statistic::registerMobileDetails($registry->session->visitId, $deviceInfo);
		}

		//redirect to mobile controller
		if($registry->configuration->resources->useragent->wurflapi->redirect)
		{
			header('location: '.$registry->configuration->website->params->url.'/mobile');
			exit;
		}
	}
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
$actionControllerPath = CONTROLLERS_PATH . '/' . $registry->route['module'] . '/' . $registry->route['controller'] . 'Controller.php';
if(file_exists($actionControllerPath))
{
	$dotAuth = Dot_Auth::getInstance();
	$dotAuth->checkIdentity('user');
	require($actionControllerPath);
}
else
{
	Dot_Route::pageNotFound();
}

// set menus
$tpl->setViewMenu();

// set SEO html tags from dots/seo.xml file
$tpl->setSeoValues($pageTitle);

// display message (error, warning, info)	
$tpl->displayMessage();

// parse the main content block
$tpl->parse('MAIN_CONTENT', 'tpl_main');

// show debugbar 
$debug = new Dot_Debug($tpl);
$debug->show();

// parse and print the output
$tpl->pparse('OUTPUT', 'tpl_index');

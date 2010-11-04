<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */
 
 /**
 * Main public executable wrapper.
 * Setup environment, setup index controllers , and  load module to run
 * @author     DotKernel Team <team@dotkernel.com>
 */

// Start counting the time needed to display all content, from the very beginning
$startTime = microtime();

// Define application environment
defined('APPLICATION_ENV') || 
	define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define application path	
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/library',
    get_include_path(),
)));

//Set error reporting
if(APPLICATION_ENV != 'production') error_reporting(-1);

//Set include  path to library directory
set_include_path(
	implode(PATH_SEPARATOR, array(realpath(dirname(__FILE__).'/library'), get_include_path())));

// Define PATH's (absolute paths)  to configuration, controllers, DotKernel, templates  directories
defined('CONFIGURATION_PATH') || define('CONFIGURATION_PATH', realpath(dirname(__FILE__).'/configs'));
defined('CONTROLLERS_PATH') || define('CONTROLLERS_PATH', realpath(dirname(__FILE__).'/controllers'));
defined('DOTKERNEL_PATH') || define('DOTKERNEL_PATH', realpath(dirname(__FILE__).'/DotKernel'));
defined('TEMPLATES_PATH') || define('TEMPLATES_PATH', realpath(dirname(__FILE__).'/templates'));

// Define DIRECTORIES  ( relative paths)
defined('TEMPLATES_DIR') || define('TEMPLATES_DIR', '/templates');
defined('IMAGES_DIR') || define('IMAGES_DIR', '/images');

// Load Zend Framework
require_once 'Zend/Loader/Autoloader.php';
$zend_loader = Zend_Loader_Autoloader::getInstance();

//includes all classes in library folder. That class names must start with Dot_
$zend_loader->registerNamespace('Dot_');

// Create registry object, as read-only object to store there config, settings, and database
$registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
Zend_Registry::setInstance($registry);

//Load configuration settings from application.ini file and store it in registry
$config = new Zend_Config_Ini(CONFIGURATION_PATH.'/application.ini', APPLICATION_ENV);
$registry->configuration = $config;

//Load routes(modules, controllers, actions) settings from router.xml file and store it in registry
$router = new Zend_Config_Xml(CONFIGURATION_PATH.'/router.xml');
$registry->router = $router;

// Create  connection to database, as singleton , and store it in registry
$db = Zend_Db::factory('Pdo_Mysql', $config->database->params->toArray());
$registry->database = $db;

//Load specific configuration settings from database, and store it in registry
$settings = Dot_Settings::getSettings();
$registry->settings = $settings;

//Set PHP configuration settings from application.ini file
Dot_Settings::setPhpSettings($config->phpSettings->toArray());

// Start Index Controller
$requestRaw = explode('/', 
					  trim(substr($_SERVER['REQUEST_URI'], 
					  strlen(dirname($_SERVER['PHP_SELF']))), '/'));
					  

// We are in frontend or in other module ? Prebuilt modules: frontend, admin, rss, ...
$requestModule = 'frontend';
if (in_array($requestRaw['0'], $config->resources->modules->toArray()))
{
	$requestModule = strtolower(basename(stripslashes($requestRaw['0'])));
}
// if  we are NOT in frontend  module
if ($requestModule != 'frontend')
    array_shift($requestRaw);
    
// set Controller and Action value, default Index
$requestController = 'Index';
if (isset($requestRaw['0']) && $requestRaw['0'] != '')
{
	$requestController = strtolower(basename(stripslashes($requestRaw['0'])));
}

// set Action value, default nothing
$requestAction = '';
if (isset($requestRaw['1']) && $requestRaw['1'] != '')
{
	$requestAction = strtolower(basename(stripslashes($requestRaw['1'])));
}
else
{	//take the default action from router.xml
	$requestAction = $registry->router->routes->action->{$requestModule}->{ucfirst($requestController)};
}

// we have extra variables, so we load all in the global array $request
$request = array();
while (list($key, $val) = each($requestRaw))
{
    $request[$val] = strtolower(current($requestRaw));
    next($requestRaw);
}

// remove first element of the request array, is module and action in it
array_shift($request);

//memory request into param variable and load them into registry
$route = array();
$route['module'] = $requestModule;
$route['controller'] = $requestController;
$route['action'] = $requestAction;
$route = array_merge($route, $request);
$registry->route = $route;

// start dotKernel object
$dotKernel = new Dot_Kernel();

// initialize default options for dots that may be overwritten
$option = Dot_Settings::getOptionVariables($route['module'], 'default');
$registry->option = $option;

// initialize the session
Dot_Session::start();
$session = Zend_Registry::get('session');

// set seo routes and initialize seo options
$seo = new Dot_Seo();
$seo->routes();
$registry->seo = $seo->getOption();

/**
*  From this point , the control is taken by the Front Controller
*  call the Front Controller specific file, but check first if exists 
*/

$frontControllerPath = CONTROLLERS_PATH.'/'.$requestModule.'/'.'IndexController.php';

!file_exists($frontControllerPath) ?  $dotKernel->pageNotFound() :  require($frontControllerPath);

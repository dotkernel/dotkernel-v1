<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel v1.0
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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

//Initialize the session
Dot_Sessions::start();
$session = Zend_Registry::get('session');

//Load configuration settings from application.ini file and store it in registry
$config = new Zend_Config_Ini(CONFIGURATION_PATH.'/application.ini', APPLICATION_ENV);
$registry->configuration = $config;

//Load resource(modules, controllers, actions) settings from resource.ini file and store it in registry
$resource = new Zend_Config_Xml(CONFIGURATION_PATH.'/resource.xml');
$registry->resource = $resource;

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

// We are in frontend or in other module ? Prebuilt modules: frontend, admin, rss, ajax 
$requestModule = 'frontend';
if (in_array($requestRaw['0'], $config->resources->modules->toArray()))
{
	$requestModule = basename(stripslashes($requestRaw['0']));
}
// if  we are NOT in frontend  module
if ($requestModule != 'frontend')
    array_shift($requestRaw);
    
// set Controller and Action value, default Index
$requestController = 'Index';
if (isset($requestRaw['0']) && $requestRaw['0'] != '')
{
	$requestController = ucfirst(basename(stripslashes($requestRaw['0'])));
}

// set Action value, default nothing
$requestAction = '';
if (isset($requestRaw['1']) && $requestRaw['1'] != '')
{
	$requestAction = basename(stripslashes($requestRaw['1']));
}

// we have extra variables, so we load all in the global array $request
$request = array();
while (list($key, $val) = each($requestRaw))
{
    $request[$val] = current($requestRaw);
    next($requestRaw);
}

// remove first element of the request array, is module and action in it
array_shift($request);

// load all extra parameters in registry
$registry->param = $request;

// Start dotKernel object
$dotKernel = new Dot_Kernel();

/**
*  From this point , the control is taken by the Front Controller
*  call the Front Controller specific file, but check first if exists 
*/
$frontControllerPath = CONTROLLERS_PATH.'/'.$requestModule.'/'.'IndexController.php';
!file_exists($frontControllerPath) ?  $dotKernel->pageNotFound() :  require($frontControllerPath);

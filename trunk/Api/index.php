<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Api
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * API controller
 * example usage:
 *    /var/www/example.com/api/index.php?action=opcache&key=XXXXXDKXXXXX
 * @author     DotKernel Team <team@dotkernel.com>
 */

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define application path
define('APPLICATION_PATH', realpath(dirname(__DIR__)));

// Define API path
define('API_PATH', realpath(dirname(__FILE__)));

//Set include  path to library directory
set_include_path(implode(PATH_SEPARATOR, array(APPLICATION_PATH . '/library', get_include_path())));

// Define PATH to configuration
define('CONFIGURATION_PATH', APPLICATION_PATH . '/configs');

chdir(dirname(__DIR__));

// Load Zend Framework
require_once 'Zend/Loader/Autoloader.php';
$zendLoader = Zend_Loader_Autoloader::getInstance();
$zendLoader->registerNamespace('Dot_');
$zendLoader->registerNamespace('Api_');

// Create registry object, as read-only object to store there config, settings, and database
$registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
Zend_Registry::setInstance($registry);

// Load configuration settings from application.ini file and store it in registry
$config = new Zend_Config_Ini(CONFIGURATION_PATH.'/application.ini', APPLICATION_ENV);
$registry->configuration = $config;

// check if api enabled from application.ini 
if($registry->configuration->api->params->enable != true)
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Get the action and the other arguments
$params = array();
$params = $_GET;
if (!empty($params))
{
	$registry->action = $params['action'];
	unset($params['action']);
	$registry->arguments = $params;
}

// check the API key
if($registry->configuration->api->params->key != $registry->arguments['key'])
{
	header("HTTP/1.0 401 Unauthorized");
	$data = array();
	$data[] = array('result' => 'error');
	$data[] = array('response' => "Invalid Key");
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
	exit;
}

// check rate limit 
if($registry->configuration->api->params->lifetime == strtolower('minute'))
{
	$timeKey = date("i");
	$ttl = 60;
}
else
{
	// h - 12 hrs ; H - 24 hrs 
	$timeKey = date("H");
	$ttl = 3600;
}

$cacheRateKey = $registry->configuration->cache->namespace. '_'. $registry->configuration->api->params->prefix 
				.'_'.$registry->arguments['key'] .$timeKey ; 

// using apcu directly
// for more info about the caching layer see    http://www.dotkernel.com/tag/dotkernel-caching/
$rate = (int)(apc_fetch($cacheRateKey));
$rateLimit = $registry->configuration->api->params->rate_limit ;
if($rate > $rateLimit)
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}
apc_store($cacheRateKey,1+$rate, $ttl);

// Create  connection to database, as singleton , and store it in registry
$db = Zend_Db::factory('Pdo_Mysql', $config->database->params->toArray());
$registry->database = $db;

// Load specific configuration settings from database, and store it in registry
$settings = Dot_Settings::getSettings();
$registry->settings = $settings;
$registry->option = array();

// Set PHP configuration settings from application.ini file
Dot_Settings::setPhpSettings($config->phpSettings->toArray());

include('Controller.php');
<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define application path	
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

//Set error reporting
if(APPLICATION_ENV != 'production') error_reporting(-1);

//Set include  path to library directory
set_include_path(implode(PATH_SEPARATOR, array(realpath(dirname(__FILE__).'/library'), get_include_path())));

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

// initialize the DotKernel Enviromnment
$registry = Dot_Kernel::initialize();
$registry->startTime = $startTime;
// Pass controll to the controller
/*
                .''
      ._.-.___.' (`\
     //(        ( `'
    '/ )\ ).__. )
    ' <' `\ ._/'\
       `   \     \
*/
Dot_Kernel::gallop($registry);
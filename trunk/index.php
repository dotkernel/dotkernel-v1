<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotKernel
 * @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Main public executable wrapper.
 * Setup environment, setup index controllers, and load modules to run
 * @author     DotKernel Team <team@dotkernel.com>
 */

// Start counting the time needed to display all content, from the very beginning
$startTime = microtime(true);

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define application path	
define('APPLICATION_PATH', realpath(dirname(__FILE__)));

//Set error reporting
if(APPLICATION_ENV != 'production') error_reporting(- 1);

//Set include  path to library directory
set_include_path(implode(PATH_SEPARATOR, array(APPLICATION_PATH . '/library', get_include_path())));

// Define PATH's (absolute paths)  to configuration, controllers, DotKernel, templates  directories
define('CONFIGURATION_PATH', APPLICATION_PATH . '/configs');
define('CONTROLLERS_PATH', APPLICATION_PATH . '/controllers');
define('DOTKERNEL_PATH', APPLICATION_PATH . '/DotKernel');
define('TEMPLATES_PATH', APPLICATION_PATH . '/templates');

// Define DIRECTORIES  ( relative paths)
define('TEMPLATES_DIR', '/templates');
define('IMAGES_DIR', '/images');

// Load Zend Framework
require_once 'Zend/Loader/Autoloader.php';
$zend_loader = Zend_Loader_Autoloader::getInstance();

//includes all classes in library folder. That class names must start with Dot_
$zend_loader->registerNamespace('Dot_');

// initialize the DotKernel Enviromnment
Dot_Kernel::initialize($startTime);

// Pass control to the controller
/*
                .''
      ._.-.___.' (`\
     //(        ( `'
    '/ )\ ).__. )
    ' <' `\ ._/'\
       `   \     \
*/
Dot_Kernel::gallop();
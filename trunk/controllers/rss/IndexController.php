<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Rss
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Rss Module - Index Controller
* Is doing all the job for specific rss control stuff 
* @author     DotKernel Team <team@dotkernel.com>
*/

/**
 * start the template object,
 * NOTE: the output of this module is XML not HTML
 * This View class does not inherit from Dot_Template class
 */
require(DOTKERNEL_PATH . '/' . $registry->requestModule . '/' . 'View.php');
$view = View::getInstance(TEMPLATES_PATH . '/' . $registry->requestModule);
/** 
 * each Controller  must load its own specific models and views
 */
Dot_Settings :: loadControllerFiles($registry->requestModule);

$option = Dot_Settings::getOptionVariables($registry->requestModule, $registry->requestControllerProcessed);
$registry->option = $option;
/**
 * From this point , the control is taken by the Action specific controller
 * call the Action specific file, but check first if exists
 */
$actionControllerPath = CONTROLLERS_PATH . '/' . $registry->requestModule . '/' . $registry->requestControllerProcessed . 'Controller.php';
!file_exists($actionControllerPath) ?  Dot_Route::pageNotFound() : require($actionControllerPath);
//output the rss content
$view->output();
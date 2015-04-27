<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* SEO stuff. MetaKeyword, MetaDescription, Canonical URL, and other stuff related SEO.
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
* @todo		  URL Rewrite
*/

class Dot_Route
{
	/**
	 * Constructor - private
	 * 
	 * This class only contains static methods
	 * therefore we don't need any instances of it
	 * 
	 * @access private
	 * @return Dot_Route
	 */
	private function __construct ()
	{
	}

	/**
	 * Parses the request URI and sets the route
	 * Gets the module, action, controller and request parameters
	 * from the request URI
	 */
	public static function setRoute()
	{
		$registry = Zend_Registry::getInstance();
		
		$realRequest = substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['PHP_SELF'])));
		$getRequest = '?' . http_build_query($_GET);
		$tmpRequest = str_replace($getRequest, '', $realRequest);
		// remove the GET param from url - not to take in consideration when spliting the url request
		// into module - controller - action
		$requestRaw = explode('/', trim($tmpRequest, '/'));

		// We are in frontend or in other module ? Prebuilt modules: frontend, admin, rss, ...
		$requestModule = 'frontend';
		if (in_array($requestRaw['0'], $registry->configuration->resources->modules->toArray()))
		{
			$requestModule = strtolower(basename(stripslashes($requestRaw['0'])));
		}
		// if we are NOT in the frontend  module
		if ($requestModule != 'frontend')
		{
			array_shift($requestRaw);
		}

		// set Controller and Action value, default Index
		$requestController = 'Index';
		if (isset($requestRaw['0']) && $requestRaw['0'] != '')
		{
			$requestController = $requestRaw['0'];
		}
		
		// set Action value, default nothing
		$requestAction = '';
		if (isset($requestRaw['1']) && $requestRaw['1'] != '')
		{
			$requestAction = strtolower(basename(stripslashes($requestRaw['1'])));
		}
		else
		{
			//take the default action from router.xml
			$requestAction = $registry->router->routes->action->{$requestModule}->{ucfirst($requestController)};
		}
		// we have extra variables, so we load all in the global array $request
		$request = array();
		$requestCount = count($requestRaw);
		for($i=0; $i<$requestCount; $i++)
		{
			$key = urldecode($requestRaw[$i]);
			if($key == '')
			{
				continue; // "just ignore it"
			}  
			$request[$key] = ($i+1<$requestCount) ? urldecode($requestRaw[++$i]) : '';
		}
		
		// remove first element of the request array, is module and action in it
		array_shift($request);
		$registry->request = $request;
		$registry->requestModule = $requestModule;
		$registry->requestController = $requestController;
		$registry->requestAction = $requestAction;
		Dot_Route::__setDefaultRoutes();
	}

	/**
	 * Set the default route
	 * Finalize the routes using the default routes set in route.xml
	 * @access public
	 * @return void
	 */
	private static function __setDefaultRoutes()
	{
		$router = Zend_Registry::get('router');
		// set Module and Action default values
		$requestModule = Zend_Registry::get('requestModule');
		$requestController = Zend_Registry::get('requestController');
		$requestAction = Zend_Registry::get('requestAction');
		
		$defaultController = isset($router->routes->controller->$requestModule) ?
									$router->routes->controller->$requestModule : '';
		
		$requestController = isset($requestController) && $requestController !='Index' ? 
									$requestController : $defaultController;
		
		$defaultAction = isset($router->routes->action->$requestModule->$requestController) ? 
								$router->routes->action->$requestModule->$requestController: '';
		
		$requestAction = isset($requestAction) && $requestAction !='' ? $requestAction : $defaultAction;

		// processed controller, eg BlogArticle
		$requestControllerProcessed = Dot_Route::processController($requestController);
		
		Zend_Registry::set('requestControllerProcessed', $requestControllerProcessed);
		Zend_Registry::set('requestController', $requestController);
		Zend_Registry::set('requestAction', $requestAction); 
	}
	
	/**
	 * Create canonical URL
	 * This method will be changed when will add URL ReWrite alternative
	 * @todo improvement of canonical url's
	 * @access public
	 * @param array $link [optional]
	 * @return string 
	 */
	public static function createCanonicalUrl($link = null)
	{
		$registry = Zend_Registry::getInstance();
		$route = array(
			'module' => $registry->requestModule,
			'controller' => $registry->requestController,
			'action' => $registry->requestAction
		);
		$route = ($link == '') ? $route : $link;

		$url = Zend_Registry::get('configuration')->website->params->url;
		// if the url does not contain a tailing slash we will put it
		// http://dotkernel.com ->http://dotkernel.com/ 
		if( '/' != substr($url, -1, 1))
		{
			$url .= '/';
		}
		if('frontend' != $route['module'])
		{
			$url .=  urlencode(strtolower($route['module'])) . '/';
		}
		if( '' != $route['controller'])
		{
			$url .= urlencode(strtolower($route['controller'])) . '/';
		}
		if( '' != $route['action'])
		{
			$url .= urlencode(strtolower($route['action'])) . '/';
		}
		foreach (Zend_Registry::get('request') as $k => $v)
		{	
			if ($k !== "page")
				$url .= urlencode($k) . '/' . urlencode($v) . '/';
		}
		return $url;
	}
	
	/**
	 * Get SEO options
	 * @access public
	 * @return array
	 */
	public static function getOption()
	{
		$registry = Zend_Registry::getInstance();
		$option = Dot_Settings::getOptionVariables($registry->requestModule, 'seo');

		//remove the 'option' xml atribute

		$option->__unset('option');

		$option->__set('canonicalUrl',Dot_Route::createCanonicalUrl());

		return $option;
	}
	
	/**
	 * Process controller
	 * Formats a controller name (eg: admin -> Admin, store-product > StoreProduct
	 * Should be moved to Dot_Route
	 * @access pubic
	 * @param $controllerName - string
	 * @return string
	 */
	public static function processController($controllerName)
	{
		$controllerName = basename(stripslashes($controllerName));
		$segments = explode('-', $controllerName);
		$result = '';
		foreach ($segments as $segment)
		{
			$result .= ucfirst(strtolower($segment));
		}
		return $result;
	}

	/**
	 * End the execution of the application,
	 * by sending an 404 header and redirecting to home page
	 * @access public
	 * @param string $who [optional]
	 * @return bool
	 */
	public static function pageNotFound($who = '')
	{
		$config = Zend_Registry::get('configuration');
		// send the 404 header
		header('HTTP/1.0 404 Not Found');
		// redirect to 404 page
		echo '<SCRIPT LANGUAGE=JAVASCRIPT>
					function go()
					{
						window.location.href="'.$config->website->params->url.'/'.$who.'"
						}
						</SCRIPT>
					</HEAD>
					<BODY onLoad="go()">
					<!--
					- Unfortunately, Microsoft has added a clever new
					- \"feature\" to Internet Explorer. If the text of
					- an error\'s message is \"too small\", specifically
					- less than 512 bytes, Internet Explorer returns
					- its own error message. You can turn that off,
					- but it\'s pretty tricky to find switch called
					- \"smart error messages\". That means, of course,
					- that short error messages are censored by default.
					- IIS always returns error messages that are long
					- enough to make Internet Explorer happy. The
					- workaround is pretty simple: pad the error
					- message with a big comment like this to push it
					- over the five hundred and twelve bytes minimum.
					- Of course, that\'s exactly what you\'re reading
					- right now.
					 -->';
		exit;
	}
	
	/**
	 * Returns a list with all Controllers defined for a module
	 * @access public
	 * @return array
	 */
	public static function getControllersForModule($module)
	{
		$router = Zend_Registry::get('router');
		if( is_string ($router->controllers->$module))
		{
			return array($router->controllers->$module);
		}
		return $router->controllers->$module->toArray();
	}
}
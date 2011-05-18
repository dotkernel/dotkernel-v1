<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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

class Dot_Seo
{	
	/**
	 * Option variable from dots/seo.xml file
	 * @access private
	 * @var Zend_Config
	 */
	private $_option = NULL;
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Seo
	 */
	public function __construct ()
	{
		//get the content of dots/seo.xml file into the option variable
		
		$this->config = Zend_Registry::get('configuration');
		$this->router = Zend_Registry::get('router');
		$this->route = Zend_Registry::get('route');
		$this->request = Zend_Registry::get('request');
		$this->_option = Dot_Settings::getOptionVariables($this->route['module'], 'seo');
	}

	/**
	 * Parses the request URI
	 * Gets the module, action, controller and request parameters
	 * from the request URI
	 */
	public static function parseUri()
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
			$requestController = Dot_Seo::processController($requestRaw['0']);
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
		while (list($key, $val) = each($requestRaw))
		{
			$request[$val] = current($requestRaw);
			next($requestRaw);
		}

		// remove first element of the request array, is module and action in it
		array_shift($request);
		//memory request into param variable and load them into registry
		$route = array();
		$route['module'] = $requestModule;
		$route['controller'] = $requestController;
		$route['action'] = $requestAction;
		$registry->request = $request;
		$registry->route = $route;
	}

	/**
	 * Make the route by module/controller/action
	 * Update the new route
	 * @access public
	 * @return void
	 */
	public function routes()
	{
		// set Module and Action default values
		$requestModule = $this->route['module'];
		$requestController = $this->route['controller'];
		$requestAction = $this->route['action'];
		
		$defaultController = isset($this->router->routes->controller->$requestModule) ?
		                           $this->router->routes->controller->$requestModule : '';
		
		$requestController = isset($requestController) && $requestController !='Index' ? 
		                           $requestController : $defaultController;
		
		$defaultAction = isset($this->router->routes->action->$requestModule->$requestController) ? 
		                           $this->router->routes->action->$requestModule->$requestController: '';
		
		$requestAction     = isset($requestAction) && $requestAction !='' ? $requestAction : $defaultAction;
		
		$this->route['controller'] = $requestController;
		$this->route['action'] = $requestAction;
		Zend_Registry::set('route', $this->route);
	}
	/**
	 * Create canonical URL
	 * This method will be changed when will add URL ReWrite alternative
	 * @todo improvement of canonical url's
	 * @access public
	 * @param array $link [optional]
	 * @return string 
	 */
	public function createCanonicalUrl($link = NULL)
	{
		$route = ($link == '') ? $this->route : $link;	
		$url = $this->config->website->params->url;
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
		foreach ($this->request as $k => $v)
		{
			$url .= urlencode($k) . '/' . urlencode($v) . '/';
		}		
		return $url;
	}
	/**
	 * Get SEO options
	 * @access public
	 * @return array
	 */
	public function getOption()
	{		
		//remove 'option' xml atribute
		$this->_option->__unset('option');
		if(isset($this->_option->canonicalUrl))
		{
			// add canonical url to the array from dots/seo.xml file
			$this->_option->__set('canonicalUrl',$this->createCanonicalUrl());
		}		
		return $this->_option;
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
			$result .= ucfirst($segment);
		}
		return $result;
	}
}
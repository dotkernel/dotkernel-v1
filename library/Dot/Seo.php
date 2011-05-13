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
		
		$defaultAction = isset($this->router->routes->action->$requestModule->$requestController) ? $this->router->routes->action->$requestModule->$requestController: '';
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
}
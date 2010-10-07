<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	private $option = NULL;
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Sessions
	 */
	public function __construct ()
	{
		//get the content of dots/seo.xml file into the option variable
		$this->option = Dot_Settings::getOptionVariables('frontend', 'seo');
		$this->config = Zend_Registry::get('configuration');
		$this->route = Zend_Registry::get('route');
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
			$url .=  urlencode($route['module']) . '/';
		}
		if( '' != $route['controller'])
		{
			$url .= urlencode($route['controller']) . '/';
		}
		if( '' != $route['action'])
		{
			$url .= urlencode($route['action']) . '/';
		}
		//unset the request route: module, controller and action
		unset($route['module']);
		unset($route['controller']);
		unset($route['action']);	
		foreach ($route as $k => $v)
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
		$this->option->__unset('option');
		if(isset($this->option->canonicalUrl))
		{
			// add canonical url to the array from dots/seo.xml file
			$this->option->__set('canonicalUrl',$this->createCanonicalUrl());
		}		
		return $this->option;
	}		
}
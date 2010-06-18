<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* SEO stuff. MetaKeyword, MetaDescription, Canonical URL, and other stuff related SEO.
* 
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
		$this->param = Zend_Registry::get('param');
	}
	/**
	 * Create canonical URL
	 * This method will be changed when will add URL ReWrite alternative
	 * @todo improvement of canonical url's
	 * @access private
	 * @return string 
	 */
	private function createCanonicalUrl()
	{
		$param = $this->param;
		$url = $this->config->website->params->url;
		if( '/' != substr($url, -1, 1))
		{
			$url .= '/';
		}
		if('frontend' != $param['module'])
		{
			$url .=  $param['module'] . '/';
		}
		if( '' != $param['controller'])
		{
			$url .= $param['controller'] . '/';
		}
		if( '' != $param['action'])
		{
			$url .= $param['action'] . '/';
		}
		//unset the request params: module, controller and action
		unset($param['module']);
		unset($param['controller']);
		unset($param['action']);		
		foreach ($param as $k => $v)
		{
			$url .= $k . '/' . $v . '/';
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
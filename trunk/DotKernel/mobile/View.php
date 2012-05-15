<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Mobile
* @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* View Model
* abstract over the Dot_Template class
* @category   DotKernel
* @package    Mobile
* @author     DotKernel Team <team@dotkernel.com>
*/

class View extends Dot_Template
{
	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Template
	 */
	protected static $_instance = null;
	/**
	 * Returns an instance of Dot_View
	 * Singleton pattern implementation
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) {
			self::$_instance = new self($root, $unknowns, $fallback);
		}
		return self::$_instance;
	}
	/**
	 * Initalize some parameter
	 * @access public
	 * @return void
	 */
	public function init()
	{
		$this->requestModule = Zend_Registry::get('requestModule');
		$this->requestController = Zend_Registry::get('requestController');
		$this->requestAction = Zend_Registry::get('requestAction');
		$this->config = Zend_Registry::get('configuration');
		$this->seo = Zend_Registry::get('seo');
	}
	/**
	 * Set the template file
	 * @access public
	 * @return void
	 */
	public function setViewFile()
	{
		$this->setFile('tpl_index', 'index.tpl');
	}
	/**
	 * Set different paths url(site, templates, images)
	 * @access public
	 * @return void
	 */
	public function setViewPaths()
	{
		$this->setVar('TEMPLATES_URL', $this->config->website->params->url . TEMPLATES_DIR);
		$this->setVar('IMAGES_URL', $this->config->website->params->url . IMAGES_DIR . '/' .$this->requestModule);
		$this->setVar('SITE_URL', $this->config->website->params->url);
	}
	/**
	 * Set SEO values
	 * @access public
	 * @param string $pageTitle [optional]
	 * @return void
	 */
	public function setSeoValues($pageTitle = '')
	{
		$this->setVar('PAGE_KEYWORDS', $this->seo->defaultMetaKeywords);
		$this->setVar('PAGE_DESCRIPTION', $this->seo->defaultMetaDescription);
		$this->setVar('PAGE_TITLE', $this->seo->defaultPageTitle .  ' | ' . $pageTitle);
		$this->setVar('PAGE_CONTENT_TITLE', $pageTitle);
		$this->setVar('SITE_NAME', $this->seo->siteName);
		$this->setVar('CANONICAL_URL', $this->seo->canonicalUrl);
	}
	/**
	 * Display message - error, warning, info
	 * @access public
	 * @param bool $ajax [optional] - Using ajax, parse only the list content
	 * @return void
	 */
	public function displayMessage()
	{
		$session = Zend_Registry::get('session');
		if(isset($session->message))
		{
			$this->setFile('tpl_msg', 'blocks/message.tpl');
			$this->setBlock('tpl_msg', 'msg_array', 'msg_array_row');
			$this->setVar('MESSAGE_TYPE', $session->message['type']);
			if(is_array($session->message['txt']))
			{
				foreach ($session->message['txt'] as $k => $msg)
				{
					$this->setVar('MESSAGE_ARRAY', is_string($k) ? $msg = ucfirst($k) . ' - ' . htmlspecialchars($msg) : htmlspecialchars($msg));
					$this->parse('msg_array_row', 'msg_array', true);
				}
			}
			else
			{
				$this->parse('msg_array_row', '');
				$this->setVar('MESSAGE_STRING', htmlspecialchars($session->message['txt']));
			}
			$this->parse('MESSAGE_BLOCK', 'tpl_msg');
			unset($session->message);
		}
	}
}
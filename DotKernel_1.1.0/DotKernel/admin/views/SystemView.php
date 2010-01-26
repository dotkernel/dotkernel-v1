<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Admin 
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class System_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
	}
	/**
	 * Display dashboard
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function dashboard($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		// system overview
		$db = Zend_Registry::get('database');
		$t=$db->fetchRow('select version() as ve');
		$this->tpl->setVar('MYSQL',$t['ve']);
		$this->tpl->setVar('PHP',phpversion());
		$this->tpl->setVar('PHPAPI',php_sapi_name());
		$this->tpl->setVar('ZFVERSION', Zend_Version::VERSION);
		#$this->parseRss('http://www.zend.com/en/company/news/press/feed');		
	}
	/**
	 * Get the Rss by using Zend_Feed_Atom and parse it
	 * @access private
	 * @param string $link
	 * @return void
	 */
	private function parseRss($link)
	{
		/**
		 * @todo check if can't connect to the url link
		 */
		// Fetch the latest Slashdot headlines
		try
		{
			$feed = new Zend_Feed_Atom($link);
		} 
		catch (Zend_Feed_Exception $e) 
		{
		    // feed import failed
		    die ("Exception caught importing feed: {$e->getMessage()}");
		}
		$this->tpl->setBlock('tpl_main', 'rss_link', 'rss_link_block');
		foreach ($feed as $k => $v) 
		{
			$this->tpl->setVar('BG', $k%2+1);
			$this->tpl->setVar('RSS_TITLE', $v->title());
			$this->tpl->setVar('RSS_LINK', $v->link('alternate'));
			$this->tpl->parse('rss_link_block', 'rss_link', true);
		}		
	}
}

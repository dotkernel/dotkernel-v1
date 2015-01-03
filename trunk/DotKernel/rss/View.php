<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* View class - used for outputting rss content
* @category   DotKernel
* @package    Rss
* @author     DotKernel Team <team@dotkernel.com>
*/
class View
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
		if (null === self::$_instance)
		{
			self::$_instance = new self($root, $unknowns, $fallback);
		}
		return self::$_instance;
	}
	/**
	 * Set the feed content
	 * @access public
	 * @param array $feed
	 * @return void
	 */
	public function setFeed($feed)
	{
		$this->feed = $feed;
	}
	/**
	 * Output the RSS content - with correct headers
	 * @access public
	 * @return void
	 */
	public function output()
	{
		$feedObject = Zend_Feed::importArray($this->feed, 'rss');
		//output the content
		$feedObject->send();
	}
}

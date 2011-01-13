<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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

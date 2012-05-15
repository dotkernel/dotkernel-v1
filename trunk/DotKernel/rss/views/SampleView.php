<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Rss
* @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Sample View Class
* @category   DotKernel
* @package    Rss
* @author     DotKernel Team <team@dotkernel.com>
*/

class Sample_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($view)
	{
		$this->view = $view;
	}
	
	/**
	 * Set the feed content
	 * @access public
	 * @param array $feed
	 * @return void
	 */
	public function setFeed($feed)
	{
		$this->view->setFeed($feed);
	}
}
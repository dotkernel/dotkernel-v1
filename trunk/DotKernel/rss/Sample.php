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
* Sample Model
* Here are all the actions related to the Sample
* @category   DotKernel
* @package    Rss
* @author     DotKernel Team <team@dotkernel.com>
*/
class Sample extends Dot_Model
{
	/**
	 * Constructor
	 * @access public
	 * @return sample
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Get sample items
	 * @access private
	 * @return array
	 */
	private function _getItems()
	{
		//some sample items used for the test
		$data = array(
					array('title' => 'About Us', 'link' => '/page/about/', 'content' =>'This is a Sample description for About Us page'),
					array('title' => 'Who we are', 'link' => '/page/who-we-are/', 'content' =>'This is a description for Who we are Sample page')
					);
		return $data;
	}
	/**
	 * Set entries for rss sample
	 * @access public
	 * @param array $entries
	 * @return array
	 */
	public function setEntries()
	{
		$entries = array();
		//get sample entries
		$items = $this->_getItems();
		foreach ($items as $item)
		{
			$entry = array();
			$entry['title'] = $item['title'];
			$entry['link'] = $this->config->website->params->url.$item['link'];
			$entry['description'] = $item['content'];
			$entries[] = $entry;
		}
		return $entries;
	}
}

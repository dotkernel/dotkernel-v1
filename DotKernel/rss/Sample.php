<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Rss
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
class Sample
{
	/**
	 * Constructor
	 * @access public
	 * @return sample
	 */
	public function __construct()
	{
		$this->config = Zend_Registry::get('configuration');		
	}
	/**
	 * Get sample items
	 * @access private
	 * @return array
	 */
	private function getItems()
	{
		//some sample items used for test
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
		$items = $this->getItems();
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
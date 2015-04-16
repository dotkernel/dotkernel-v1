<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * DotKernel RSS Reader Model
 * @category   DotKernel
 * @package    DotPlugin
 * @subpackage RSS_Reader
 * @author     DotKernel Team <team@dotkernel.com>
 */
class Plugin_DotKernel_RssReader_Model
{
	/**
	 * Feed Array
	 * 
	 * This array is used to keep the feed. This prevents multiple remote requests 
	 * @var array $_feed 
	 */
	private $_feed = array();
	
	/**
	 * Feed url
	 * @var string $_url
	 */
	private $_url = '';
	
	/**
	 * Constructor
	 * @param string $url [optional] - the url to read feed from 
	 */
	public function __construct($url = 'http://www.dotkernel.com/feed/')
	{
		$this->_url = $url; 
		$this->getFeed(true);
	}
	
	/**
	 * Rss Keys
	 * @var array
	 */
	private $_rssKeys = array(
		'title',
		'link',
		'guid',
		'comments',
		'description',
		'pubDate',
		'category',
	);
	
	/**
	 * Get feed from URL
	 * 
	 * Returns false on failure 
	 * @access private
	 * @return string|false
	 */
	private function _fetchFeed()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($httpCode == 200)
		{
			return $response;
		}
		return false;
	}
	
	/**
	 * Process feed array
	 * @param string $feed
	 */
	private function _processFeed($feed) 
	{
		$rssItemTag = 'item';
		$rssPosts = array();
		$items = array();
		
		$doc = new DOMdocument();
		$doc->loadXML($feed);
		
		foreach($doc->getElementsByTagName($rssItemTag) as $node)
		{
			foreach($this->_rssKeys as $key)
			{
				$items[$key] = $node->getElementsByTagName($key)->item(0)->nodeValue;
			}
			$rssPosts[] = $items;
		}
		return $rssPosts;
	}
	
	/**
	 * Get RSS Feed 
	 * @param string $refresh
	 * @return multitype:|boolean
	 */
	public function getFeed($refresh = false)
	{
		if($refresh == true)
		{
			$feed = $this->_fetchFeed();
			// if the feed is not empty process it  
			// do not overwrite if feed is invalid
			if( $feed )
			{
				$this->_feed = $this->_processFeed($feed);
			}
		}
		if(count($this->_feed) > 0 )
		{
			return $this->_feed;
		}
		return false;
	}
}
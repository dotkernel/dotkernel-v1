<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    DotLibrary
 * @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */
 
 /**
 * Process the data used for pagination
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotPaginator
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Paginator
{	
	/**
	 * @var Zend_Paginator
	 * @access private
	 */
	private $_paginator;
	/**
	 * Curent Page
	 * @access private 
	 * @var int
	 */
	private $_currentPage = 0;
	/**
	 * Items per page 
	 * @access private 
	 * @var int
	 */
	private $_itemCountPerPage = 0;
	/**
	 * @access private 
	 * @var object ArrayIterator
	 */
	private $_currentItems = null;
	/**
	 * Dot_Paginator constructor that sets the main parameters
	 * If $page is 0 (zero), Zend_Paginator will not be called
	 * @access public 
	 * @param Zend_Db_Select $select
	 * @param int $page [optional]
	 * @param int $resultsPerPage [optional]
	 * @return Dot_Paginator
	 */	
	public function __construct($select, $page = 0, $resultsPerPage = 0)
	{		
		$this->db = Zend_Registry::get('database');
		$this->_select = $select;
		$this->_currentPage = $page;
		$this->_itemCountPerPage = $resultsPerPage;
		$settings = Zend_Registry::get('settings');
		if($this->_currentPage > 0)
		{
			$adapter = new Zend_Paginator_Adapter_DbSelect($select);
			$this->_paginator = new Zend_Paginator($adapter);
			$this->_paginator->totalItems = $adapter->count();
			// page range = the pages on the left + the pages on the right + the current page
			$this->_paginator->setPageRange($settings->paginationStep * 2 + 1);
		}
	}
	/**
	 * Get current items to display on current page
	 * @access public 
	 * @return object ArrayIterator
	 */
	public function getCurrentItems()
	{			
		$this->_paginator->setItemCountPerPage($this->_itemCountPerPage);
		$this->_paginator->setCurrentPageNumber($this->_currentPage);
		if ($this->_currentItems === null) 
		{
        	$this->_currentItems = $this->_paginator->getCurrentItems();
		}		
		return $this->_currentItems;		
	}
	/**
	 * Create the page object used in View - paginator method
	 * @access public
	 * @return object
	 */
	public function getPages()
	{
		$pages = new stdClass();
		$pageCount = $this->_paginator->count();
		$pages->pageCount = $pageCount;
		$pages->itemCountPerPage = $this->_itemCountPerPage;
		$pages->first = 1;
		$pages->current = (int)$this->_currentPage;
		$pages->last = $pageCount;
		
		// Previous and next
		if ($this->_currentPage - 1 > 0) 
		{
			$pages->previous = $this->_currentPage - 1;
		}
		if ($this->_currentPage + 1 <= $pageCount) 
		{
			$pages->next = $this->_currentPage + 1;
		}
		
		// Pages in range
		$pageRange = $this->_paginator->getPageRange();
		if ($pageRange > $pageCount)
		{
			$pageRange = $pageCount;
		}
		$delta = ceil($pageRange / 2);
		if ($this->_currentPage - $delta > $pageCount - $pageRange)
		{
			$lowerBound = $pageCount - $pageRange + 1;
			$upperBound = $pageCount;
		}
		else
		{
			if ($this->_currentPage - $delta < 0)
			{
				$delta = $this->_currentPage;
			}
				$offset     = $this->_currentPage - $delta;
				$lowerBound = $offset + 1;
				$upperBound = $offset + $pageRange;
		}
		$pages->pagesInRange     = $this->_paginator->getPagesInRange($lowerBound, $upperBound);
		$pages->firstPageInRange = min($pages->pagesInRange);
		$pages->lastPageInRange  = max($pages->pagesInRange);

		// Item numbers
		if ($this->_currentItems == null) 
		{
			$this->getCurrentItems();
		}
			if ($this->_currentItems !== null) 
		{
			$pages->currentItemCount = $this->_paginator->getCurrentItemCount();
			$pages->itemCountPerPage = $this->_paginator->getItemCountPerPage();
			$pages->totalItemCount   = $this->_paginator->getTotalItemCount();
			$pages->firstItemNumber  = (($this->_currentPage - 1) * $this->_paginator->getItemCountPerPage()) + 1;
			$pages->lastItemNumber   = $pages->firstItemNumber + $pages->currentItemCount - 1;
		}

		return $pages;
	}
	/**
	 * Return a multiple array with data and pages
	 * @access public
	 * @return array
	 */
	public function getData()
	{		
		$pages = array();
		$data = array();
		if($this->_currentPage > 0)
		{ 			
			$data = $this->getCurrentItems();
			$pages = $this->getPages();
		}
		else
		{
			$data = $this->db->fetchAll($this->_select);
		}
		return array('data'=> $data, 'pages'=> $pages);
	}	
}
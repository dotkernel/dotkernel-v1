<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
* @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Sample Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$sampleModel = new Sample();
$sampleView = new Sample_View($view);

//prepare feed
$feed = array(
			'title' => $option->feed->title,
			'link' => $registry->configuration->website->params->url.$option->feed->link,
			'charset' => $option->feed->charset,
			'language' => $option->feed->language
			);
$feed['published'] = time();
$entries = $sampleModel->setEntries();
$feed['entries'] = $entries;
//set the feed that will be outputed
$sampleView->setFeed($feed);
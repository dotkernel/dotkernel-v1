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
* Page Controller
* @author     DotKernel Team <team@dotkernel.com>
*/

$pageView = new Page_View($tpl);
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$registry->route['action']};
switch ($registry->route['action'])
{
	default:
	case 'home';
		// call showPage method to view the home page
		$pageView->showPage('home');
	break;
	case 'about':
		$pageView->showPage($registry->route['action']);
	break;
	case 'who-we-are':
		$pageView->showPage($registry->route['action']);
	break;
	case 'outbound-links':
		$pageView->showPage($registry->route['action']);
	break;
	case 'image':
		$dotImage = new Dot_Image();
		// set the image that will be manipulated
		$imagePath = APPLICATION_PATH.'/images/frontend/MVC-structure.png';
		$imageNewPath = APPLICATION_PATH.'/images/test/MVC-structure_resize.png';
		$dotImage->setImage($imagePath);
		$dotImage->setDestinationImage($imageNewPath);
		// initiate the parameters for resizing the image
		$resizeOption = array('width' => 140, 'height' => 500, 'measure' => 'px', 'preview' => TRUE);
		$dotImage->setOption($resizeOption);
		// resize the image
		$dotImage->resize();
		$pageView->showImage($registry->route['action'], $dotImage->getImage(), $dotImage->getImage($imagePath));	
}
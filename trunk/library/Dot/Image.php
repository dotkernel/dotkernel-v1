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
 * Image manipulation
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotImage
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Image
{
	/**
	 * Image filename
	 * @var string
	 * @access public
	 */
	public $source = '';
	/**
	 * New image, after transformation
	 * @var string
	 * @access public
	 */
	public $destination = '';
	/**
	 * Image options
	 * @var array
	 * @access private
	 */
	private  $_option = array();
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Image
	 */
	public function __construct()
	{
		$option = Zend_Registry::get('option');
		if(!extension_loaded('gd'))
		{
			die($option->warningMessage->modGd);
		}
	}
	/**
	 * Set the image file
	 * @access public
	 * @param string $filename
	 * @return void
	 */
	public function setImage($filename)
	{
		$this->source = $filename;
	}
	/**
	 * Set the image file
	 * @access public
	 * @param string $filename
	 * @return void
	 */
	public function setDestinationImage($filename)
	{
		$this->destination = $filename;
	}
	/**
	 * Set the image options
	 * @param object $option
	 * @return void
	 */
	public function setOption($option)
	{
		$this->_option = $option;
	}
	/**
	 * Get the transformed image - new image. URL link to it
	 * @access public
	 * @param string $src - source of the image
	 * @return string
	 */
	public function getImage($src = '')
	{
		$config = Zend_Registry::get('configuration');
		$image = $src == '' ? $this->destination : $src;
		return str_replace(APPLICATION_PATH, $config->website->params->url, $image);
	}
	/**
	 * Resize the image. Return possible errors encountered
	 * @access public
	 * @return void
	 */
	public function resize()
	{
		$dotImageResize = new Dot_Image_Resize();
		$dotImageResize->setImage($this->source);
		$dotImageResize->setDestinationImage($this->destination);
		$dotImageResize->setOption($this->_option);
		return $dotImageResize->resize();
	}
}
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
 * Resize images
 * @category   DotKernel
 * @package    DotLibrary
 * @subpackage DotImage
 * @author     DotKernel Team <team@dotkernel.com>
 */

class Dot_Image_Resize extends Dot_Image
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
	 * Is an array with the following keys
	 * 			- width: integer
	 * 			- height: integer
	 * 			- measure: 'px', '%'
	 * 			- preview: bool [TRUE][FALSE] - if true, preserve the ratio aspect
	 * 			- exactSize: bool [TRUE][FALSE] - if true, make the image the 
	 * 			  			 exact size, even if it will be twist
	 * @var array
	 * @access private
	 */
	private  $_option = array
							('width' => 100,
							 'height' => 100,
							 'measure' => '%', 
							 'preview' => FALSE,
							 'exactSize' => FALSE
							 );
	/**
	 * Source image
	 * @var resource
	 * @access private
	 */
	public $_imageSource;
	/**
	 * Destination image
	 * @var resource
	 * @access private
	 */
	public $_imageDestination;
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Image_Resize
	 */
	public function __construct()
	{
	}
	/**
	 * Set the image options
	 * @param object $option
	 * @return void
	 */
	public function setOption($option)
	{
		foreach ($option as $key =>$value)
		{
			$this->_option[$key] = $value;
		}
	}
	/**
	 * Get string contents from original image
	 * @access public
	 * @return string
	 */
	private function _getImageString ()
	{
		$handle = fopen($this->source, "r");
		$contents = fread($handle, filesize($this->source));
		fclose($handle);
		return $contents;
	}
	/**
	 * Resize image. Return possible errors encountered
	 * @access public
	 * @return array
	 */
	public function resize()
	{		
	
		#get errors for create image
		$oldTrackErrorSetting = ini_set('track_errors', '1');
		
		#create image from string
		$this->_imageSource = @imagecreatefromstring($this->_getImageString());
		if ($this->_imageSource === false)
		{
			#manual error variable, do not change it: http://php.net/manual/en/reserved.variables.phperrormsg.php
			$errorMessage = explode(':', $php_errormsg);
			$errorMessage = $errorMessage[(count($errorMessage) - 1)];
			return array('error' => $errorMessage);
		}
		
		#change track errors back to default
		ini_set('track_errors', $oldTrackErrorSetting);
		
		try{
			#get new dimensions
			$oldWidth = imagesx($this->_imageSource);
			$oldHeight = imagesy($this->_imageSource);
			#if we need an exact redimension
			if ($this->_option['exactSize'] !== FALSE) 
			{
				$thumbnailWidth = $this->_option['width'];
				$thumbHeight = $this->_option['height'];
			}
			#redimension using largest dimension as a standard
			else
			{
				if($oldWidth > $oldHeight)
				{
					$thumbnailWidth = $this->_option['width'];
					$thumbHeight = $oldHeight * ($this->_option['height'] / $oldWidth);
				}	
				if($oldWidth < $oldHeight)
				{
					$thumbnailWidth = $oldWidth * ($this->_option['width'] / $oldHeight);
					$thumbHeight = $this->_option['height'];
				}	
				if($oldWidth == $oldHeight)
				{
					$thumbnailWidth = $this->_option['width'];
					$thumbHeight = $this->_option['height'];
				}
			}
			if($this->_option['preview'] === false)
			{
				$this->_imageDestination = imagecreatetruecolor($thumbnailWidth, $thumbHeight);
				imagecopyresized($this->_imageDestination, $this->_imageSource, 0, 0, 0, 0, $thumbnailWidth, $thumbHeight, $oldWidth, $oldHeight);
			}
			#custom resize for offer preview which needs to have exact dimensions
			else
			{
				#we need to cut from image, so we have a perfect image for the preview
				#we need to get the aspect ratio between image size and given width
				$aspectRatio = $oldWidth / $this->_option['width'];
				#the image content bellow this point wont be needed
				$cuttedImageHeight = floor($this->_option['height'] * $aspectRatio);

				$this->_imageDestination = imagecreatetruecolor($this->_option['width'], $this->_option['height']);
				imagecopyresampled($this->_imageDestination, $this->_imageSource, 0, 0, 0, 0, $this->_option['width'], $this->_option['height'], $oldWidth, $cuttedImageHeight);

				#fill this image white, it may not be an entire image from the shrinktheweb.com, so leave a white background, not a black one
				#a black one would look like an unfinished image
				if ($oldHeight < $cuttedImageHeight)
				{
					$white = imagecolorallocate($this->_imageSource, 255, 255, 255);
					$rectanglewidth = ceil(($cuttedImageHeight - $oldHeight) / $aspectRatio);
					imagefilledrectangle($this->_imageDestination, 0, $this->_option['height'] - $rectanglewidth, $this->_option['width'], $this->_option['height'], $white);
				}
			}
			#save new image
			$resizedImage = @imagejpeg($this->_imageDestination, $this->destination);

			if ($resizedImage === false) 
			{
				#return custom image
				return array('error' => 'Unable to save image !');
			}			
		}
		catch (Exception $fail)
		{
			$message = 'Image resize error: '.$fail->getMessage();
			return array('error' => $message);
		}
		#return no error
		return array();
	}
}
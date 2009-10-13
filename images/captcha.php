<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Images
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Captcha file used for secure image code
* @author     DotKernel Team <team@dotkernel.com>
* @TODO Write captcha file, improve it
*/
$security_image_code_length = 5;
// Generate Reference ID
if (isset($_GET['code']) && ($_GET['code']!= ''))
{
	$referenceid = stripslashes($_GET['code']);	
}
else 
{
	$referenceid = md5(mktime()*rand());
}	
// Generate the random string
$chars = array('1','2','3','4','5','6','7','8','9','0');
$length = $security_image_code_length;
// Select Font
$font = './arial.ttf';
// Select random background image
$bgurl = rand(1, 3);
$im = imageCreateFromPNG('security_code/bg'.$bgurl.'.png');
$textstr = '';
for ($i=0; $i<$length; $i++)
{
	$textstr .= $chars[rand(0, count($chars)-1)];
}
// Create random size, angle, and dark color
$size = rand(12, 16);
$angle = rand(-5, 5);
$color = ImageColorAllocate($im, rand(0, 100), rand(0, 100), rand(0, 100));
// Determine text size, and use dimensions to generate x & y coordinates
$textsize = imagettfbbox($size, $angle, $font, $textstr);
$twidth = abs($textsize[2]-$textsize[0]);
$theight = abs($textsize[5]-$textsize[3]);
$x = (imagesx($im)/2)-($twidth/2)+(rand(-20, 20));
$y = (imagesy($im))-($theight/2);
// Add text to image
imageTTFText($im, $size, $angle, $x, $y, $color, $font, $textstr);
// Output PNG Image
$_SESSION['kernel']['security_code'] = $referenceid;
// Delete references older than 1 day
header('Content-Type: image/png');
imagePNG($im);
// Destroy the image to free memory
imagedestroy($im);
?>
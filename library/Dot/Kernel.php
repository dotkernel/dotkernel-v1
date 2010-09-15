<?php 
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Bunch of miscelaneous  functions, used in all DotKernel Applications
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/
 
class Dot_Kernel
{
	/**
     * Configuration instance
     * @var Zend_config
     */
    public $config;
	/**
     * Constant
     * Dot Kernel version identification
     * @var string 
     */
    const VERSION = '1.2.3';    
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Kernel
	 */
    public function __construct()
    {
    	$this->config = Zend_Registry::get('configuration');
    }
    /**
     * End the execution of the application, by sending an 404 header and redirecting to home page
     * @access public
     * @return bool
     */
    public function pageNotFound()
    {   	
        // send the 404 header
        header('HTTP/1.0 404 Not Found');
        // redirect to 404 page
        echo '<SCRIPT LANGUAGE=JAVASCRIPT> 
					function go()
					{
						window.location.href="'.$this->config->website->params->url.'" 
						}
						</SCRIPT>
					</HEAD>
					<BODY onLoad="go()">
					<!--
   				- Unfortunately, Microsoft has added a clever new 
   				- \"feature\" to Internet Explorer. If the text of
   				- an error\'s message is \"too small\", specifically
   				- less than 512 bytes, Internet Explorer returns
   				- its own error message. You can turn that off,
   				- but it\'s pretty tricky to find switch called
   				- \"smart error messages\". That means, of course,
   				- that short error messages are censored by default.
   				- IIS always returns error messages that are long
  				- enough to make Internet Explorer happy. The
   				- workaround is pretty simple: pad the error
   				- message with a big comment like this to push it
   				- over the five hundred and twelve bytes minimum.
   				- Of course, that\'s exactly what you\'re reading
   				- right now.
  				 -->';
        exit;
    }
	/**
	 * Check if IP is valid. Return FALSE | 'private' | 'public'
	 * FALSE - $ip is not a valid IP address
	 * private - $ip is a private network IP address
	 * public - $ip is a public network IP address
	 * @access public
	 * @static 
	 * @param string $ip
	 * @return mixt [FALSE | 'private' | 'public']
	 */
	 public static function validIp($ip)
	{
		$privateIpRange = array(
					array('10.0.0.0','10.255.255.255'),
					array('172.16.0.0','172.31.255.255'),
					array('192.168.0.0','192.168.255.255'),
					'127.0.0.1'
					);
		$validatorIp = new Zend_Validate_Ip();
		$currentIp = (float)sprintf("%u",ip2long($ip)); 
		if($validatorIp->isValid($ip))
		{			
			foreach ($privateIpRange as $ipRange)
			{
				if(is_array($ipRange))
				{					
					$shortRangeIp = (float)sprintf("%u",ip2long($ipRange[0]));
					$longRangeIp = (float)sprintf("%u",ip2long($ipRange[1]));
					if($currentIp >= $shortRangeIp && $currentIp <= $longRangeIp)
					{	// it is a private IP
						return 'private';
					}
				}
				elseif($ip == $ipRange)
				{	// it is a private IP
					return 'private';
				}				
			}
		}
		else
		{	// Zend_Validate_Ip rejected it
			return FALSE;
		}
		return 'public';
	}
    /**
     * Return the user Ip, even if it is behind a proxy
     * @access public
     * @static
     * @return string
     */
    public static function getUserIp()
    {
    	if (isset($_SERVER))
        {    
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && self::validIp($_SERVER['HTTP_X_FORWARDED_FOR']) == 'public')
            {
            	// check if HTTP_X_FORWARDED_FOR is public network IP	
                $realIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }			
            elseif (isset($_SERVER['HTTP_CLIENT_IP']) && self::validIp($_SERVER['HTTP_CLIENT_IP']) == 'public')
            {	
            	// check if HTTP_CLIENT_IP is public network IP	
                $realIp = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                $realIp = $_SERVER['REMOTE_ADDR'];
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR') && self::validIp(getenv('HTTP_X_FORWARDED_FOR')) == 'public')
            {
            	// check if HTTP_X_FORWARDED_FOR is public network IP	
                $realIp = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP') && self::validIp(getenv('HTTP_CLIENT_IP')) == 'public')
            {
            	// check if HTTP_CLIENT_IP is public network IP	
                $realIp = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realIp = getenv('REMOTE_ADDR');
            }
        }
        return $realIp;
    }
	/**
	 * Return the name of the browser icon based on User Agent
	 * @access public
	 * @static
	 * @param string $user
	 * @return string
	 */
	public static function getBrowserIcon($agent)
	{		
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/browser.xml');
		$browser = $xml->name->type->toArray();
		foreach ($browser as $key => $val)
		{			
			if (stripos($agent,$val['uaBrowser']) !== FALSE)
			{
				return $val['uaIcon'];
			}
		}
		return 'unknown';
	}
	/**
	 * Return the name of the OS icon based on User Agent
	 * @access public
	 * @static
	 * @param string $user
	 * @return array
	 */
	public static function getOsIcon($agent)
	{		
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/os.xml');
		$os = $xml->type->toArray();
		foreach ($os as $major)
		{
			foreach ($major as $osArray)
			{
				if(array_key_exists('identify', $osArray))
				{//there are minor version
					foreach ($osArray['identify'] as $minor)
					{
						$uaStringArray = explode('|',$minor['uaString']);
						foreach ($uaStringArray as $uaString)
						{						
							if ((stripos($agent, $uaString) !== false))
				            {
				                $operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])), 'major'=>$osArray['os'], 'minor'=>$minor['osName']);
								return $operatingSystem;
				            }
						}					
					}
				}
				else
				{//no minor version known for this os
					if ((stripos($agent, $osArray['os']) !== false))
		            {
		                $operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])), 'major'=>$osArray['os'], 'minor'=>'');
						return $operatingSystem;
		            }
				}
									
			}
		}
		return array('major'=>'', 'minor'=>'');
	}
	/**
	 * Return date formatted fancy
	 * @access public
	 * @static
	 * @param string $date
	 * @param string $format - 'short', 'long'
	 * @return string
	 */
	public static function timeFormat($date, $format='short')
	{
		$settings = Zend_Registry::get('settings');
		$times = strtotime($date);
		switch($format)
		{
			case 'long':
				$times = strftime($settings->timeFormatLong,$times);
			break;			
			case 'short':
			default:
				$times = strftime($settings->timeFormatShort,$times);
			break;
		}
		return $times;
	}
	/**
	 * Process that validate and filter the input/output data.
	 * Return valid and filtered data
	 * @access public
	 * @static
	 * @param Zend_Validate $validator
	 * @param array $values
	 * @return array
	 */
	public static function validateFilter($validator, $values)
	{
		$data = $error = array();
		$filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_HtmlEntities());
        $filter->addFilter(new Zend_Filter_StringTrim());
		foreach ($values as $k=>$v)
		{
		    if($validator->isValid($values[$k]))
			{
				//filter the input     
				$data[$k] = $filter->filter($values[$k]); 
			}
			else
			{
				foreach ($validator->getMessages() as $message)
				{
					//filter the output
					$error[$k] = str_replace($values[$k], $filter->filter($values[$k]), $message);
				}
			}
		}
		return array('data'=>$data,'error'=>$error);
	}
}

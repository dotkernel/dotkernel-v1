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
    const VERSION = '1.3.3 dev';
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
	 * private - $ip is a private network IP address, loopback address or an IPv6 IP address
	 * public - $ip is a public network IP address
	 * @access public
	 * @static 
	 * @param string $ip
	 * @return mixt [FALSE | 'private' | 'public']
	 */
	public static function validIp($ip)
	{
		// special cases that return private are the loopback address and IPv6 addresses
		if ($ip == '127.0.0.1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			return 'private';
		}
		// check if the ip is valid
		if (filter_var($ip, FILTER_VALIDATE_IP))
		{
			// check wether it's private or not
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE))
			{
				return 'public';
			}
			return 'private';
		}
		// not a valid ip
		return false;
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
	public static function getBrowserIcon($agent, $return = 'icon')
	{		
		$xml = new Zend_Config_Xml(CONFIGURATION_PATH.'/browser.xml');
		$browser = $xml->name->type->toArray();
		foreach ($browser as $key => $val)
		{			
			if (stripos($agent,$val['uaBrowser']) !== FALSE)
			{				
				if('browser' == $return) 
				{
					return $val['uaBrowser'];
				}
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
				// if we have only one menu, Zend_Config_Xml return a simple array, not an array with key 0(zero)
				if (!array_key_exists('0', $osArray['identify'])) 
				{					
					//we create the array with key 0
					$osIdentify[] = $osArray['identify'];
				}
				else
				{
					$osIdentify = $osArray['identify'];
				}
					foreach ($osIdentify as $minor)
					{		
						//check if there are different strings for detecting an operating system							
						if(strstr($minor['uaString'],'|') !== FALSE)
						{
							$uaStringArray = explode('|',$minor['uaString']);	
							foreach ($uaStringArray as $uaString)
							{						
								if ((stripos($agent, $uaString) !== false))
					            {
					                $operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])), 
															 'major'=>$osArray['os'], 
															 'minor'=>$minor['osName']);
									return $operatingSystem;
					            }
							}	
						}
						else
						{	
							if ((stripos($agent, $minor['uaString']) !== false))
				            {
				                $operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])), 
														 'major'=>$osArray['os'], 
														 'minor'=>$minor['osName']);
								return $operatingSystem;
				            }
						}					
					}
				}
				else
				{//no minor version known for this os
					if ((stripos($agent, $osArray['os']) !== false))
		            {
		                $operatingSystem = array('icon'=>strtolower(str_replace(' ', '_', $osArray['os'])), 
												 'major'=>$osArray['os'], 
												 'minor'=>'');
						return $operatingSystem;
		            }
				}				
			}
		}
		return array('icon'=>'unknown', 'major'=>'', 'minor'=>'');
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
	 * Generate a token for a user
	 * @access public
	 * @static
	 * @param string $password - the users's password or password hash
	 * @return array
	 */
	public static function generateUserToken($password)
	{
		$config = Zend_Registry::get('configuration');
		// use the user's password hash and the site database password
		return sha1($config->database->params->password.$password);
	}
	/**
	 * Check if a user's token is set and is correct
	 * @access public
	 * @static
	 * @return void
	 */
	public static function checkUserToken($type='admin')
	{
		$user = Dot_Auth::getIdentity($type);
		if (!isset($_POST['userToken']) || (Dot_Kernel::generateUserToken($user->password)!=$_POST['userToken']))
		{
			exit;
		}
	}
	/**
	 * Get HTTP UserAgent Device.
	 * Device may be Bot, Checker, Console, Desktop, Email, Feed, Mobile, Offline, Probe, Spam, Text, Validator
	 * If device is mobile, return will be Zend_Http_UserAgent_Mobile	
	 * @return Zend_Http_UserAgent_...
	 */
	public static function getDevice()
	{
		$config = Zend_Registry::get('configuration');
		$userAgent = new Zend_Http_UserAgent($config->resources->useragent);
		return $userAgent->getDevice();
	}
}

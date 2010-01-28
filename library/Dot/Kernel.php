<?php 
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
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
	 * Database instance
	 * @var Zend_Db
	 */
    public $db;
    /**
     * Configuration instance
     * @var Zend_config
     */
    public $config;
    /**
     * Settings variables
     * @var object
     */
    public $settings;    
    /**
     * Dot Kernel version identification
     */
    const VERSION = '1.1.1';    
	/**
	 * Constructor
	 * @access public
	 * @return void
	 */
    public function __construct()
    {
        $this->config = Zend_Registry::get('configuration');
        $this->settings = Zend_Registry::get('settings');
        $this->db = Zend_Registry::get('database');
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
     * Return the user Ip , whatever the server are set
     * @access public
     * @static
     * @return string
     */
    public static function getUserIp()
    {
        if (isSet($_SERVER))
        {
            if (isSet($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $realIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isSet($_SERVER['HTTP_CLIENT_IP']))
            {
                $realIp = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                $realIp = $_SERVER['REMOTE_ADDR'];
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realIp = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
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
	 * Process the validation
	 * @access public
	 * @static
	 * @param Zend_Validate $validator
	 * @param array $values
	 * @return array
	 */
	public static function validate($validator, $values)
	{
		$data = $error = array();
		foreach ($values as $k=>$v)
		{
		    if($validator->isValid($values[$k]))
			{
				$data[$k] = $values[$k]; 
			}
			else
			{
				foreach ($validator->getMessages() as $message)
				{
					//filter the output
					$filter = new Zend_Filter();
					$filter->addFilter(new Zend_Filter_HtmlEntities());
					$filter->addFilter(new Zend_Filter_StringTrim());
					$error[$k] = str_replace($values[$k], $filter->filter($values[$k]), $message);
				}
			}
		}
		return array('data'=>$data,'error'=>$error);
	}
}

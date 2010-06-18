<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* CURL with TOR and country proxy features
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/
class Dot_Curl
{
	/**
	 * User agent
	 * @access public
	 * @var array
	 */
	public $userAgents = array(
		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3',
		'Mozilla/5.0 (Windows; U; Windows NT 5.0; rv:1.7.3) Gecko/20040913 Firefox/0.10',
		'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
		'Microsoft Internet Explorer/Version (Platform)',
		'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)',
		'msnbot/1.0 (+http://search.msn.com/msnbot.htm)',
		'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)'
	);
	/**
	 * Used in case you want to specify userAgent
	 * @access public
	 * @var string
	 */
	public $defaultUserAgent = '';
	/**
	 * Used in case you want to bypass tor proxy
	 * @access public
	 * @var bool
	 */
	public $useTor = true;
	/**
	 * Use in case you want to use country proxy feature
	 * @access public
	 * @var integer
	 */
	public $countryId = 0;
	/**
	 * localhost ip
	 * @access public
	 * @var string
	 */
	public $torIp = '127.0.0.1';
	/**
	 * an array with available ports
	 * @access public
	 * @var array
	 */
	public $ports = array(9000, 9001, 9002, 9003, 9004, 9005, 9006, 9007, 9008, 9009, 9010, 9011, 9012, 9013, 9014, 9015, 9016, 9017, 9018, 9019, 9020);
	/**
	 * General settings: sslVerifyPeer
	 * @access public
	 * @var bool
	 */
	public $sslVerifyPeer = false;
	/**
	 * General settings: header
	 * @access public
	 * @var bool
	 */
	public $header = true;
	/**
	 * General settings: returnTransfer
	 * @access public
	 * @var bool
	 */
	public $returnTransfer = true;
	/**
	 * General settings: followLocation
	 * @access public
	 * @var bool
	 */
	public $followLocation = true;
	/**
	 * General settings: timeOut
	 * @access public
	 * @var int
	 */
	public $timeOut = 200;
	/**
	 * Errors , view these in case no data is shown
	 * @access public
	 * @var array
	 */
	public $errors = array();
	/**
	 * Info , stores curl_getinfo() response
	 * @access public
	 * @var array
	 */
	public $info = array();
	/**
	 * Post vars , leave empty if not needed
	 * @access public
	 * @var array
	 */
	public $postVars = array();
	/**
	 * Cookies , leave empty if not needed
	 * @access public
	 * @var array
	 */
	public $cookies = array();
	/**
	 * Set curl options
	 * @access private
	 * @param object $ch
	 * @param string $url
	 * @param string $referer [optional]
	 * @return void
	 */
	private function setOptions($ch, $url, $referer = '')
	{
		//if no referer is provided, use the url as the referer
		if ($referer == '') 
		{
			$referer = $url;
		}
		//general options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, $this->header);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->returnTransfer);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
		//follow redirects , be carefull to se open_basedir to none and that php is not in safe mode
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
		//get a random user agent
		if ($this->defaultUserAgent != '')
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $this->defaultUserAgent);
		}
		else 
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgents[rand(0, count($this->userAgents) -1)]);
		}

		//if useTor is true connection will be done throu tor proxy
		if ($this->useTor)
		{
			//for multy curl we build an array of already used ports to avoid using the same ports more then once
			if (count($this->usedPorts) > 0 && count($this->usedPorts) < count($this->ports))
			{
				$i = 0;
				$ok = false;
				while (!$ok && $i < 20)
				{
					$port = $this->ports[rand(0, (count($this->ports) - 1))];

					if (!in_array($port, $this->usedPorts)) 
					{
						$ok = true;
					}
					$i++;
				}
			}
			//get a random port
			else 
			{
				$port = $this->ports[rand(0, (count($this->ports) - 1))];
			}
			$this->usedPorts[] = $port;

			curl_setopt ($ch, CURLOPT_PROXY, $this->torIp.':'.$port);
			curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}

		if(count($this->cookies) > 0)
		{
			$cc = array();
			foreach($this->cookies as $k=>$v) $cc[] ="$k=$v";
			curl_setopt ($ch, CURLOPT_COOKIE, implode('; ',$cc));
		}
		if(count($this->postVars) > 0)
		{
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $this->postVars);
		}
	}
	/**
	 * Fetch multiple urls at once
	 * @access public
	 * @param array $urls
	 * @param array $referers [optional]
	 * @return array with requests results
	 */
	public function getMulti($urls, $referers = array())
	{
		//reset every time to avoid stacking
		$this->errors = array();
		$this->usedPorts = array();
		//initialize multy curl
		$mh = curl_multi_init();
		foreach ($urls as $key => $val)
		{
			$url = $urls[$key];
			$referer = '';
			if (array_key_exists($val, $referers))
			{
				$referer = $referers[$key];
			}
			$obj[$key] = curl_init($url);
			//set options for each url
			$this->setOptions($obj[$key], $url, $referer);
			//add it to the group
			curl_multi_add_handle($mh, $obj[$key]);
		}
		//execute all the urls at once
		$running=null;
		do 
		{
			curl_multi_exec($mh,$running);
		}
		while ($running > 0);
		//retriev results from each request
		$htmls = array();
		foreach ($urls as $key => $val)
		{
			$htmls[$key] = curl_multi_getcontent($obj[$key]);

			if (curl_errno($obj[$key]) != 0)
			{
				curl_close($obj[$key]);
				$this->errors[] = 'Connection problem (cURL ERROR: '.curl_errno($obj[$key]).': '.curl_error($obj[$key]).')';
			}
			else 
			{
				curl_close($obj[$key]);
			}
			curl_multi_remove_handle($mh, $obj[$key]);
		}
		//close multy curl
		curl_multi_close($mh);
		//return the array wth all the results
		return $htmls;
	}
	/**
	 * Fetch a single url
	 * @access public
	 * @param string $url
	 * @param string $referer [optional]
	 * @return a string
	 */
	public function getSingle($url, $referer = '')
	{
		//reset every time to avoid stacking
		$this->errors = array();
		$this->usedPorts = array();
		$content = '';

		$obj = curl_init($url);
		$this->setOptions($obj, $url, $referer);
		if (count($this->errors) <= 0)
		{
			$content = curl_exec($obj);
			$this->info = curl_getinfo($obj);
			if (curl_errno($obj) != 0)
			{
				$this->errors[] = 'Connection problem (DOT CURL ERROR: '.curl_errno($obj).': '.curl_error($obj).')';
			}
		}
		curl_close($obj);
		return $content;
	}
}
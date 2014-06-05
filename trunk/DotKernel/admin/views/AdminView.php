<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin 
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Admin View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class Admin_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
		$this->settings = Zend_Registry::get('settings');
		$this->session = Zend_Registry::get('session');
	}
	/**
	 * Display the login form
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function loginForm($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');	
	}
	/**
	 * List the admin users
	 * @access public
	 * @param string $templateFile
	 * @param array $list
	 * @param int $page
	 * @param bool $ajax - Using ajax, parse only the list content
	 * @return void
	 */
	public function listUser($templateFile, $list, $page)
	{
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($list['pages']);
		$this->tpl->addUserToken();
		$this->tpl->setVar('PAGE', $page);
		$this->tpl->setVar('ACTIVE_URL', '/admin/admin/activate/');
		foreach ($list['data'] as $k => $v)
		{
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('EMAIL', $v['email']);
			$this->tpl->setVar('FIRSTNAME', $v['firstName']);
			$this->tpl->setVar('LASTNAME', $v['lastName']);
			$this->tpl->setVar('DATE_CREATED', Dot_Kernel::timeFormat($v['dateCreated'], 'long'));
			$this->tpl->setVar('ISACTIVE', $v['isActive']);
			$this->tpl->setVar('ACTIVE_IMG', $v['isActive'] == 1 ? 'active' : 'inactive');
			$this->tpl->parse('list_block', 'list', true);
		}
	}
	/**
	 * Display admin user details. It is used for add and update actions
	 * @access public
	 * @param string $templateFile
	 * @param array $data [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array())
	{
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');
		$this->tpl->setVar('ACTIVE_1', 'checked');
		$this->tpl->addUserToken();
		foreach ($data as $k=>$v)
		{
			$this->tpl->setVar(strtoupper($k), $v);
			if('isActive' == $k)
			{
				$this->tpl->setVar('ACTIVE_'.$v, 'checked');
				$this->tpl->setVar('ACTIVE_'.$v*(-1)+1, '');
			}
		}
		//empty because we don't want to show the password 
		$this->tpl->setVar('PASSWORD', '');
	}
	/**
	 * Display user logins list
	 * @access public
	 * @param string $templateFile
	 * @param array $list 
	 * @param int $page
	 * @return void
	 */
	public function loginsUser($templateFile, $list, $page)
	{
		$dotGeoip = new Dot_Geoip();
		$geoIpWorking = TRUE;
		$this->tpl->setFile('tpl_main', 'admin/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'list', 'list_block');
		$this->tpl->paginator($list['pages']);
		$this->tpl->setVar('PAGE', $page);
		foreach ($list['data'] as $k => $v)
		{
			$country = $dotGeoip->getCountryByIp($v['ip']);
			if($country['response'] != 'OK' && $geoIpWorking === TRUE)
			{
				$geoIpWorking = FALSE;
				$this->session->message['txt'] = $country['response'];
				$this->session->message['type'] = 'warning';
			}
			$this->tpl->setVar('ID', $v['id']);
			$this->tpl->setVar('ADMINID', $v['adminId']);
			$this->tpl->setVar('USERNAME', $v['username']);
			$this->tpl->setVar('IP', $v['ip']);
			$this->tpl->setVar('COUNTRYIMAGE', strtolower($country[0]));
			$this->tpl->setVar('COUNTRYNAME', $country[1]);
			$this->tpl->setVar('REFERER', $v['referer']);
			$this->tpl->setVar('WHOISURL', $this->settings->whoisUrl);
			$this->tpl->setVar('USERAGENT', $v['userAgent']);
			$this->tpl->setVar('BROWSERIMAGE', Dot_UserAgent::getBrowserIcon($v['userAgent']));
			$os = Dot_UserAgent::getOsIcon($v['userAgent']);
			$this->tpl->setVar('OSIMAGE', $os['icon']);
			$this->tpl->setVar('OSMAJOR', $os['major']);
			$this->tpl->setVar('OSMINOR', $os['minor']);
			$this->tpl->setVar('DATELOGIN', Dot_Kernel::timeFormat($v['dateLogin'], 'long'));
			$this->tpl->parse('list_block', 'list', true);
		}
	}
}

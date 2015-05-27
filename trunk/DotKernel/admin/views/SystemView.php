<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin 
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* System View Class
* class that prepare output related to User controller 
* @category   DotKernel
* @package    Admin 
* @author     DotKernel Team <team@dotkernel.com>
*/

class System_View extends View
{
	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
	}
	/**
	 * Display dashboard
	 * @access public
	 * @param string $templateFile
	 * @param string $mysqlVersion
	 * @param string $apcInfo
	 * @param array $geoIpVersion
	 * @param array $warnings
	 * @return void
	 */
	public function dashboard($templateFile, $mysqlVersion, $apcInfo, $geoIpVersion, $warnings, $iniValues, $cacheInfo)
	{
		// @todo: break this method in more pieces, there are too much arguments
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		// system overview
		$this->tpl->setVar('HOSTNAME' , System::getSystemHostname());
		$this->tpl->setVar('MYSQL',$mysqlVersion);
		$this->tpl->setVar('PHP',phpversion());
		$this->tpl->setVar('APCNAME', $apcInfo['name']);
		$this->tpl->setVar('APCVERSION', $apcInfo['version']);
		if ($apcInfo['enabled'] == true)
		{
			$this->tpl->setVar('APCSTATUS', 'enabled');
		}
		else
		{
			$this->tpl->setVar('APCSTATUS', 'disabled');
		}
		$this->tpl->setVar('PHPAPI',php_sapi_name());
		$this->tpl->setVar('ZFVERSION', Zend_Version::VERSION);
		
		// show warnigns
		$this->tpl->setBlock('tpl_main', 'warning_item', 'warning_item_block');
		$this->tpl->setBlock('tpl_main', 'warnings', 'warnings_block');
		foreach($warnings as $warningType => $warningItems)
		{
			if (empty($warningItems))
			{
				$this->tpl->parse('warnings_block', '', true);
			}
			else
			{
				$this->tpl->setVar('WARNING_TYPE', $warningType);
				foreach($warningItems as $warningItem)
				{
					$this->tpl->setVar('WARNING_DESCRIPTION', $warningItem);
					$this->tpl->parse('warning_item_block', 'warning_item', true);
				}
				$this->tpl->parse('warnings_block', 'warnings', true);
				$this->tpl->parse('warning_item_block', '');
			}
		}
		
		// php.ini Values
		$this->tpl->setBlock('tpl_main', 'ini_value_list', 'ini_value_list_block');
		$this->tpl->setBlock('ini_value_list', 'ini_value', 'ini_value_block');
		if(count($iniValues) > 0)
		{
			foreach($iniValues as $key => $value)
			{
				$this->tpl->setVar('INI_KEY', $key);
				$this->tpl->setVar('CURRENT_VALUE', $value['current']);
				$this->tpl->setVar('RECOMMENDED_VALUE', $value['recommended']); 
				// 1 - PHP_INI_USER		Entry can be set in user scripts (like with ini_set()) or in the Windows registry. 
				//						Since PHP 5.3, entry can be set in .user.ini
				// 2 - PHP_INI_PERDIR	Entry can be set in php.ini, .htaccess, httpd.conf or .user.ini (since PHP 5.3)
				// 4 - PHP_INI_SYSTEM	Entry can be set in php.ini or httpd.conf
				// 6 - PHP_INI_PERDIR	PHP_INI_PERDIR may not be set using ini_set()
				// 7 - PHP_INI_ALL		Entry can be set anywhere
				// List with all directives: http://php.net/manual/en/ini.list.php
				$this->tpl->setVar('EDITABLE', (in_array($value['access'], array(1, 7)) ) ? 'Yes' : 'No' );
				$this->tpl->parse('ini_value_block', 'ini_value', true);
			}
			$this->tpl->parse('ini_value_list_block', 'ini_value_list', false);
		}
		
		// Caching info
		$this->tpl->setBlock('tpl_main', 'cache_management', 'cache_management_block');
		$this->tpl->setBlock('cache_management', 'cache_key', 'cache_key_block');
		if($cacheInfo['isLoaded'])
		{
			$this->tpl->parse('cache_management_block', 'cache_management');
			$this->tpl->setVar('CACHE_TTL', $cacheInfo['lifetime']);
		}
		foreach($cacheInfo['importantKeys'] as $keyName => $key)
		{
			$this->tpl->setVar('CACHE_KEY_NAME', $keyName );
			$this->tpl->setVar('CACHE_KEY_TTL', $key['expire'] - $key['mtime'] );
			$this->tpl->setVar('CACHE_KEY_TIME_LEFT', (int)($key['expire'] - microtime(true)) );
			$this->tpl->setVar('CACHE_KEY_TTL', $key['expire'] - $key['mtime'] );
			$this->tpl->parse('cache_key_block', 'cache_key', true);
		}
		
		// GeoIP section
		$this->tpl->setVar('GEOIP_COUNTRY_LOCAL', $geoIpVersion['local']);
		
		$this->tpl->setBlock('tpl_main', 'is_geoip', 'is_geoip_row');
		if(function_exists('geoip_database_info'))
		{
			$this->tpl->setVar('GEOIP_COUNTRY_VERSION', $geoIpVersion['country']);
			$this->tpl->parse('is_geoip_row', 'is_geoip', true);
		}
		
		$this->tpl->addUserToken();
	
	}
	/**
	 * Display settings
	 * @access public
	 * @param string $templateFile
	 * @param array $data
	 * @return void
	 */
	public function displaySettings($templateFile, $data)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'textarea', 'textarea_row');
		$this->tpl->setBlock('tpl_main', 'options', 'options_row');
		$this->tpl->setBlock('tpl_main', 'option', 'option_row');
		$this->tpl->setBlock('tpl_main', 'radios', 'radios_row');
		$this->tpl->setBlock('tpl_main', 'radio', 'radio_row');
		$this->tpl->addUserToken();
		foreach ($data as $v)
		{			
			$this->tpl->setVar('NAME', $v['title']);
			$this->tpl->setVar('VARIABLE', $v['key']);
			$this->tpl->setVar('DEFAULT', $v['possibleValues']);
			$this->tpl->setVar('EXPLANATION', $v['comment']);
			switch ($v['type']) 
			{
				case 'textarea':	
					$this->tpl->setVar('CURRENT_VALUE', $v['value']);
					$this->tpl->parse('textarea_row', 'textarea', true);
				break;
				case 'option':
					$this->tpl->parse('options_row', '');
					$options = explode(';', $v['possibleValues']);
					foreach ($options as $opt)
					{
						$this->tpl->setVar('LIST_OPTION', $opt);
						$optionSelect = ($v['value'] == $opt) ? 'selected' : '';
						$this->tpl->setVar('SELECTED_OPTION', $optionSelect);
						$this->tpl->parse('options_row', 'options', true);
					}
					$this->tpl->parse('option_row', 'option', true);
				break;
				case 'radio':
					$this->tpl->parse('radios_row', '');
					$radios = explode(';', $v['possibleValues']);
					foreach ($radios as $val)
					{
						$this->tpl->setVar('POSIBLE_VALUE', $val);
						$radioTxt = ($val == 1) ? 'Yes' : 'No';
						$this->tpl->setVar('POSIBLE_VALUE_TXT', $radioTxt);
						$radioCheck = ($v['value'] == $val) ? 'checked' : '';
						$this->tpl->setVar('CHECKED_OPTION', $radioCheck);
						$this->tpl->parse('radios_row', 'radios', true);
					}
					$this->tpl->parse('radio_row', 'radio', true);
				break;
			}
		}
	}
	/**
	 * Display phpinfo values
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function showPHPInfo($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		ob_start();
		phpinfo();
		$parsed = ob_get_contents();
		ob_end_clean();
		preg_match( "#<body>(.*)</body>#is" , $parsed, $match1 );
		
		$phpBody  = $match1[1];
		// PREVENT WRAP: Most cookies	
		$phpBody  = str_replace( "; " , ";<br />"   , $phpBody );
		// PREVENT WRAP: Very long string cookies
		$phpBody  = str_replace( "%3B", "<br />"    , $phpBody );
		// PREVENT WRAP: Serialized array string cookies
		$phpBody  = str_replace( ";i:", ";<br />i:" , $phpBody );
		//$phpBody  = str_replace( ":", ";<br>" , $phpBody );
		$phpBody = preg_replace('#<table#', '<table class="list_table" align="center"', $phpBody);
		$phpBody = preg_replace('#<th#', '<td  class="table_subhead"', $phpBody);
		$phpBody = preg_replace('#(\w),(\w)#', '\1, \2', $phpBody);
		$phpBody = preg_replace('#border="0" cellpadding="3" width="600"#', 'cellspacing="0" cellpadding="0" width="100%"', 		$phpBody);
		$phpBody = preg_replace('#<hr />#', '', $phpBody);
		$phpBody = preg_replace('#<tr class="h"><td>#', '<tr><td class="row1">', $phpBody);
		$phpBody = preg_replace('#<tr class="v"><td>#', '<tr><td class="row1">', $phpBody);
		$phpBody = preg_replace('#class="e"#', 'class="row2"', $phpBody);
		$phpBody = preg_replace('#class="v"#', 'class="row1"', $phpBody);

		$this->tpl->setVar("PHPINFO", $phpBody);
	}
	/**
	 * Display the apc.php page
	 * @access public
	 * @return void
	 */
	public function showAPCInfo($apcu=null)
	{
		$this->tpl->setFile('tpl_main', 'system/apcinfo.tpl');
		$this->tpl->setVar('APC_FILE', 'apc' . $apcu);
	}

	/**
	 * Display email transporter details. It is used for update actions
	 * @param object $templateFile
	 * @param object $data [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array())
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		$this->tpl->setVar('ACTIVE_1', 'checked');
		$this->tpl->setVar('SSL_TLS', 'checked'); $this->tpl->setVar('SSL_SSL', '');
		foreach ($data as $k=>$v)
		{
			$this->tpl->setVar(strtoupper($k), $v);
			if('isActive' == $k)
			{
				$this->tpl->setVar('ACTIVE_'.$v, 'checked');
				$this->tpl->setVar('ACTIVE_'.$v*(-1)+1, '');
			}
			if('ssl' == $k)
			{
				if ($v == 'ssl')
				{
					$this->tpl->setVar('SSL_SSL', 'checked');
					$this->tpl->setVar('SSL_TLS', '');
				}
			else{
					$this->tpl->setVar('SSL_TLS', 'checked');
					$this->tpl->setVar('SSL_SSL', '');
				}
			}
		}
	}
}

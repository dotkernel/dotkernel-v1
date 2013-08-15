<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin 
* @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
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
	 * @param array $wurflInfo
	 * @param array $warnings
	 * @return void
	 */
	public function dashboard($templateFile, $mysqlVersion, $apcInfo, $geoIpVersion, $wurflInfo, $warnings)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'wurfl_api_info', 'wurfl_api');
		$this->tpl->setBlock('tpl_main', 'wurfl_cloud_info', 'wurfl_cloud');
		// system overview
		$this->tpl->setVar('HOSTNAME' , System::getSystemHostname());
		$this->tpl->setVar('MYSQL',$mysqlVersion);
		$this->tpl->setVar('PHP',phpversion());
		$this->tpl->setVar('APCNAME', $apcInfo['name']);
		$this->tpl->setVar('APCVERSION', $apcInfo['version']);
		if ($apcInfo['enabled'] == TRUE)
		{
			$this->tpl->setVar('APCSTATUS', 'enabled');
		}
		else
		{
			$this->tpl->setVar('APCSTATUS', 'disabled');
		}
		$this->tpl->setVar('PHPAPI',php_sapi_name());
		$this->tpl->setVar('ZFVERSION', Zend_Version::VERSION);
		
		if(!empty($wurflInfo['api']))
		{
			$this->tpl->setVar('WURFLCACHEBUILT', $wurflInfo['api']['cacheDate']);
			$this->tpl->setVar('WURFLDATE', $wurflInfo['api']['xmlFileDate']);
			$this->tpl->setVar('WURFLAPIVERSION', $wurflInfo['api']['apiVersion']);
			$this->tpl->parse('wurfl_api', 'wurfl_api_info');
		}
		if(!empty($wurflInfo['cloud']))
		{
			$this->tpl->setVar('WURFL_CLIENT_VERSION', $wurflInfo['cloud']['clientVersion']);
			$this->tpl->setVar('WURFL_API_VERSION', $wurflInfo['cloud']['apiVersion']);
			$this->tpl->setVar('WURFL_CLOUD_SERVER', $wurflInfo['cloud']['cloudServer']);
			$this->tpl->parse('wurfl_cloud', 'wurfl_cloud_info');
		}

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

		
		// GeoIP section
		$this->tpl->setVar('GEOIP_COUNTRY_LOCAL', $geoIpVersion['local']);
		
		$this->tpl->setBlock('tpl_main', 'is_geoip', 'is_geoip_row');
		if(function_exists('geoip_database_info'))
		{
			$this->tpl->setVar('GEOIP_CITY_VERSION', $geoIpVersion['city']);
			$this->tpl->setVar('GEOIP_COUNTRY_VERSION', $geoIpVersion['country']);
			$this->tpl->parse('is_geoip_row', 'is_geoip', true);
		}
	
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
	 * List the email transporter
	 * @param string $templateFile
	 * @param array $list
	 * @param int $page
	 * @param bool $ajax [optional] - Using ajax, parse only the list content
	 * @return void
	 */
	public function listEmailTransporter($templateFile, $list, $page)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		$this->tpl->setFile('tpl_row', 'system/transporter-row-block.tpl');
		$this->tpl->setBlock('tpl_row', 'list', 'list_block');
		$this->tpl->paginator($list['pages']);
		$this->tpl->setVar('PAGE', $page);
		$this->tpl->setVar('ACTIVE_URL', '/admin/system/transporter-activate');
		$this->tpl->setVar('ACTIVE_1', 'checked');
		$this->tpl->setVar('SSL_TLS', 'checked');
		
		foreach ($list["data"] as $k => $v)
		{
			$this->tpl->setVar('ID', $v["id"]);
			$this->tpl->setVar('USER', $v['user']);
			$this->tpl->setVar('SERVER', $v['server']);
			$this->tpl->setVar('PORT', $v['port']);
			$this->tpl->setVar('SSL', $v['ssl']);
			$this->tpl->setVar('CAPACITY', $v['capacity']);
			$this->tpl->setVar('COUNTER', $v['counter']);
			$this->tpl->setVar('DATE_CREATED', Dot_Kernel::timeFormat($v['date']));
			$this->tpl->setVar('ACTIVE_IMG', $v['isActive'] == 1 ? 'active' : 'inactive');
			$this->tpl->setVar('ISACTIVE', $v['isActive']);
			$this->tpl->parse('list_block', 'list', true);
		}
		$this->tpl->parse('TRANSPORTER_ROW', 'tpl_row');
	}
	/**
	 * Parse a transporter row block and return it
	 * @param array $data
	 * @return string
	 */
	public function getTransporterRow($data)
	{
		$this->tpl->setFile('tpl_row', 'system/transporter-row-block.tpl');
		$this->tpl->setBlock('tpl_row', 'list', 'list_block');
		
		$this->tpl->setVar('ID', $data["id"]);
		$this->tpl->setVar('USER', $data['user']);
		$this->tpl->setVar('SERVER', $data['server']);
		$this->tpl->setVar('PORT', $data['port']);
		$this->tpl->setVar('SSL', $data['ssl']);
		$this->tpl->setVar('CAPACITY', $data['capacity']);
		$this->tpl->setVar('COUNTER', $data['counter']);
		$this->tpl->setVar('DATE_CREATED', Dot_Kernel::timeFormat($data['date']));
		$this->tpl->setVar('ACTIVE_IMG', $data['isActive'] == 1 ? 'active' : 'inactive');
		$this->tpl->setVar('ISACTIVE', $data['isActive']);
		$this->tpl->parse('list_block', 'list', true);

		$this->tpl->parse('TRANSPORTER_ROW', 'tpl_row');
		return $this->tpl->get('TRANSPORTER_ROW');
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

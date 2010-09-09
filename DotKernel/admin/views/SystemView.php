<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Admin 
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
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
	 * @param array $userCountry
	 * @return void
	 */
	public function dashboard($templateFile, $mysqlVersion, $userCountry)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		// system overview
		$this->tpl->setVar('MYSQL',$mysqlVersion);
		$this->tpl->setVar('PHP',phpversion());
		$this->tpl->setVar('PHPAPI',php_sapi_name());
		$this->tpl->setVar('ZFVERSION', Zend_Version::VERSION);
		// pie chart
		$option = Zend_Registry::get('option');
		$color = $option->colorCharts->color->toArray();
		$i = 0;
		foreach ($userCountry as $code => $country)
		{
			$data[] = array('y' => $country['countPercent'], 
							'text' => $country['name'], 
							'color' => $color[$i++], 
							'tooltip' => $country['name'].": ".(string)$country['countPercent']."&#37;"
							);
		}
		$this->tpl->setVar('PIEDATA', Zend_Json::encode($data));		
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
		$phpBody  = str_replace( ":", ";<br>" , $phpBody );
		$phpBody = preg_replace('#<table#', '<table class="grey" align="center"', $phpBody);
		$phpBody = preg_replace('#<th#', '<th  class="bgmain"', $phpBody);
		$phpBody = preg_replace('#(\w),(\w)#', '\1, \2', $phpBody);
		$phpBody = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', 		$phpBody);
		$phpBody = preg_replace('#<hr />#', '', $phpBody);
		$this->tpl->setVar("PHPINFO", $phpBody);
	}
}

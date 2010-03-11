<?php
/**
* DotBoost Technologies Inc.
* DotKernel v1.0
*
* @category   DotKernel
* @package    Admin 
* @copyright  Copyright (c) 2009 DotBoost  Technologies (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* User View Class
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
	 * @return void
	 */
	public function dashboard($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		// system overview
		$db = Zend_Registry::get('database');
		$t=$db->fetchRow('select version() as ve');
		$this->tpl->setVar('MYSQL',$t['ve']);
		$this->tpl->setVar('PHP',phpversion());
		$this->tpl->setVar('PHPAPI',php_sapi_name());
		$this->tpl->setVar('ZFVERSION', Zend_Version::VERSION);
	}
	/**
	 * Display settings
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function displaySettings($templateFile, $data, $error)
	{
		$this->tpl->setFile('tpl_main', 'system/' . $templateFile . '.tpl');
		$this->tpl->setBlock('tpl_main', 'textarea', 'textarea_row');
		$this->tpl->setBlock('tpl_main', 'options', 'options_row');
		$this->tpl->setBlock('tpl_main', 'option', 'option_row');
		$this->tpl->setBlock('tpl_main', 'radios', 'radios_row');
		$this->tpl->setBlock('tpl_main', 'radio', 'radio_row');
		if($error != '')
		{
			$this->tpl->setVar('ERROR', $error);
		}
		foreach ($data as $v)
		{			
			$this->tpl->setVar('NAME', $v['title']);
			$this->tpl->setVar('VARIABLE', $v['variable']);
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
}

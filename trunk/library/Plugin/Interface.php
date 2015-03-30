<?php

/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Plugin Interface
 * 
 * @category   DotKernel
 * @package    Dot_Plugin
 * @author     DotKernel Team <team@dotkernel.com>
 */
interface Plugin_Interface
{

	/**
	 * Get plugin info
	 * @access public
	 * @return array $info
	 */
	public function getPluginInfo();

}
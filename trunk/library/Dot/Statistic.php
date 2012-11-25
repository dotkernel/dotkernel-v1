<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2012 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* General Statistics class. Interaction with Zend_Log and with application specific statistic actions
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Statistic
{
	/**
	 * Constructor
	 * @access private
	 * @return object
	 */
	private function __construct()
	{
	}

	/**
	 * call the function to record the visit
	 * @access public
	 * @return integer
	 */
	public static function registerVisit()
	{
		$visitId = Dot_Statistic_Visit::recordVisit();
		return $visitId;
	}

	/**
	 * Record device info in statisticVisitMobile table
	 * @param object $visitId
	 * @param object $device [optional]
	 * @return bool
	 */
	public static function registerMobileDetails($visitId, $device)
	{
		Dot_Statistic_Visit::recordMobileVisit($visitId, $device);
		return TRUE;
	}
}
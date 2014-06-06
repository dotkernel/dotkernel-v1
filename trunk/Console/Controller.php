<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    CLI
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 * @author     DotKernel Team <team@dotkernel.com>
 */

switch ($registry->action)
{
	case 'count-users':
		/**
		 *  example usage
		 * /var/www/vhosts/example.com/httpdocs/Console/index.php -e staging -a count-users
		 * Under Windows, run the below line in console , modifying the path to the files
		 * E:\_Zend\Apache2\htdocs\DotKernel\Console>php index.php -e development -a count-user
		 */
		
		$userModel = new Console_Model_User();
		echo "There are " . $userModel->countUsers() . " user(s) currently registered\n";
	break;

	case 'send-newsletter':
		/**
		 *  example usage
		 * /var/www/vhosts/example.com/httpdocs/Console/index.php -e staging -a send-newsletter "test newsletter"
		 */
		if (!isset($registry->arguments[0]))
		{
			echo "Please specify a newsletter\n";
			exit;
		}
		$newsletterName = $registry->arguments[0];
		$newsletterModel = new Console_Model_Newsletter();
		
		echo "sending newsletter '" . $newsletterName ."'\n";

		// code to send the newsleter using methods from cli_Newsletter should go here

	break;
	
	case 'convert-password-user':
		/**
		 * DO NOT RUN AUTOMATICALLY THIS SCRIPT 
		 * MANUALLY RUN THE BELOW QUERIES IN THE PROPER ORDER
		 * Example usage 
		 * /var/www/vhosts/example.com/httpdocs/Console/index.php -e staging -a convert-password-user "10"
		 * Under Windows, run the below line in console , modifying the path to the files
		 * E:\_Zend\Apache2\htdocs\DotKernel\Console>php index.php -e development -a convert-password-user  "10"
		 */
		
		// How many records to update into one iteration. We don't want to kill the MySQL server updating too many records at once 
		if (!isset($registry->arguments[0]))
		{
			echo "Please specify a number of records to be updated in one iteration.\n";
			exit;
		}
			
		//	1. create column passwordNew
		// ALTER TABLE `user` ADD `passwordNew` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `password`;
		
		//	2. Now you can run the script. 
		//  foreach passowrd, generate the new password 
		$userList = new Console_Model_ConvertPassword();
		$userInfo = $userList->getUsers($registry->arguments[0]);

		// No more records to update 
		if(empty($userInfo))
		{
			echo "No more records to process. \n";
			exit;
		}
		
		// we still have some records 
		$j = 0;
		foreach($userInfo as $id=>$password)
		{
			// display a warning is something was wrong, must be exactly one row affected 
			if(1 != $userList->convertPasswordUser($id, $password))
			{
				echo "Something went wrong updating password of the user ID: " . $id . "\n";
				$j--;
			}
			$j++;
		}
		echo "Updated $j records.\n";
		# 3. rename password to passwordOld
		//ALTER TABLE `user` CHANGE `password` `passwordOld` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;
		
		# 4. rename passwordNew to password
		// ALTER TABLE `user` CHANGE `passwordNew` `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
	break;

	case 'default':
		echo "Action doesn't exist.\n";
		exit(1);
	break;
}

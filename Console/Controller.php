<?php 

switch ($registry->action)
{
	case 'count-users':
		/**
		 *  example usage
		 * /var/www/vhosts/example.com/httpdocs/cli/index.php -e staging -a count-users
		 */
		
		$userModel = new Console_User();
		echo "There are " . $userModel->countUsers() . " user(s) currently registered\n";
		break;

	case 'send-newsletter':
		/**
		 *  example usage
		 * /var/www/vhosts/example.com/httpdocs/cli/index.php -e staging -a send-newsletter "test newsletter"
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
}
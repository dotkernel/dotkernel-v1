<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @copyright  Copyright (c) 2009-2015 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id$
 */

/**
 * Layer over simplified password hashing  API introduced in PHP 5.5 , including the compatibility library with 
 * older version of PHP ( > 5.3.7)
 * @category   DotKernel
 * @package    DotLibrary
 * @author     DotKernel Team <team@dotkernel.com>
 */


class Dot_Password
{

	function __construct()
	{
		//Require the compatibility library with PHP 5.5's simplified password hashing API
		if (version_compare(PHP_VERSION, '5.5.0', '<'))
		{
			require_once APPLICATION_PATH . '/library/Dot/Password/Compat.php';
		}
	}
	
	/**
	 * Hash the password, using either the built in PAssword API or the Compatibility library 
	 * @access public
	 * @see http://docs.php.net/manual/en/function.password-hash.php
	 * @param string $password - the password
	 * @param string $algo - the algorithm PASSWORD_DEFAULT is the strongest at a certain moment 
	 * @param array $options - cost and salt 
	 * Note that the salt here is randomly generated.
	 * Never use a static salt or one that is not randomly generated.
	 * For the VAST majority of use-cases, let password_hash generate the salt randomly for you
	 * EXAMPLE 
	 * $options = [
	 *						'cost' => 11,
	 *						'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
	 *						];
	 * @return string
	 */
	public function hashPassword($password, $algo = PASSWORD_DEFAULT, $options = array())
	{
		return password_hash($password, $algo, $options);
	}
	
	/**
	 * When passed in a valid hash created by an algorithm supported by password_hash(), this function 
	 * will return an array of information about that hash
	 * @see http://docs.php.net/manual/en/function.password-get-info.php
	 * @param string $hash
	 * @return array  with 3 elements:
	 *		- algo, which will match a password algorithm constant
	 * 		- algoName, which has the human readable name of the algorithm
   * 		- options, which includes the options provided when calling password_hash()
	 */
	public function hashGetInfo($hash)
	{
		return password_get_info($hash);
	}
	
	/**
	 * Verifies that the given hash matches the given password. 
	 * @see http://docs.php.net/manual/en/function.password-verify.php
	 * @param string $password
	 * @param string $hash
	 * @return boolean
	 */
	public function verifyPassword($password, $hash)
	{
		return password_verify($password, $hash);
	}
	
	/**
	 * This function checks to see if the supplied hash implements the algorithm and options provided.
	 * If not, it is assumed that the hash needs to be rehashed.
	 * @see http://docs.php.net/manual/en/function.password-needs-rehash.php
	 * @param string $hash
	 * @param string $algo
	 * @param array $options
	 * @return boolean
	 */
	public function passwordRehashNeeded($hash, $algo = PASSWORD_DEFAULT, $options = array())
	{
		return password_needs_rehash($hash, $algo, $options);
	}
}

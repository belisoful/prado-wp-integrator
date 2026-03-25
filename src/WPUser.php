<?php

use Prado\Security\TDbUser;
use Prado\Security\IUserManager;

class WPUser extends TDbUser
{
	protected $_data, $_meta;
	
	public function __construct(IUserManager $manager)
	{
		parent::__construct($manager);
	}
	
	
	public function load($data, $meta)
	{
		$this->_data = $data;
		$this->_meta = $meta;
	}
	
	public function __get($key)
	{
		if ( isset( $this->_data[$key] ) ) {
			$value = $this->_data[$key];
		} elseif ( isset( $this->_meta[$key]) ) {
			$value = $this->_meta[$key];
		}
		return $value;
	}
	
	/**
	 * Validates if username and password are correct entries.
	 * @param string $username username (case-sensitive)
	 * @param string $password password
	 * @return bool whether the validation succeeds
	 */
	public function validateUser($username, $password)
	{
		return false;
	}
	
	/**
	 * Creates a new user instance given the username.
	 *
	 * If the username is invalid (not found in the user database), null
	 * is returned.
	 *
	 * @param string $username username (case-sensitive)
	 * @return TDbUser the newly created and initialized user instance
	 */
	public function createUser($username)
	{
		return null;
	}
	
	/**
	 * Creates a new user instance given the cookie containing auth data.
	 *
	 * This method is invoked when {@link TAuthManager::setAllowAutoLogin AllowAutoLogin} is set true.
	 * The default implementation simply returns null, meaning no user instance can be created
	 * from the given cookie.
	 *
	 * @param THttpCookie $cookie the cookie storing user authentication information
	 * @return TDbUser the user instance generated based on the cookie auth data, null if the cookie does not have valid auth data.
	 * @see saveUserToCookie
	 * @since 3.1.1
	 */
	public function createUserFromCookie($cookie)
	{
		$cookieValue = $cookie->getValue();
		
		$cookie_elements = explode( '|', $cookieValue );
		if ( count( $cookie_elements ) !== 4 ) {
			return false;
		}
		$scheme	= 'auth';
		list( $username, $expiration, $token, $hmac ) = $cookie_elements;
		
		if ( $expiration < time() ) {
			return null;
		}
		$pluginModule = $this->getManager()->getManager();
		$user = $pluginModule->get_user_by( 'login', $username );
		if(!$user)
			return null;
		$pass_frag = substr( $user->user_pass, 8, 4 );
		
		$key = $pluginModule->wp_hash( $username . '|' . $pass_frag . '|' . $expiration . '|' . $token, $scheme );

		// If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
		$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$hash = hash_hmac( $algo, $username . '|' . $expiration . '|' . $token, $key );

		if ( ! hash_equals( $hash, $hmac ) )
			return null;
			
		$manager = WP_Session_Tokens::get_instance( $user->ID );
		if ( ! $manager->verify( $token ) ) 
			return null;
			
		return $user;
	}
	
	/**
	 * Saves necessary auth data into a cookie.
	 *
	 * @param THttpCookie $cookie the cookie to store the user auth information
	 * @see createUserFromCookie
	 */
	public function saveUserToCookie($cookie)
	{
		
	}
}
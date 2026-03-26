<?php

/**
 * WPUserManager class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator;

require 'composer.php';

use Prado\Security\TDbUserManager;
use Prado\TPropertyValue;

/**
 * WPUserManager class
 *
 * User manager for WordPress integration.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPUserManager extends TDbUserManager
{
	/**
	 * @var WPIntegratorModule The plugin module
	 */
	protected $_pluginModule;

	/**
	 * @var string The WordPress user roles constant
	 */
	public const WP_USER_ROLLS = 'wp_user_roles';

	/**
	 * Constructor.
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns the plugin module.
	 *
	 * @return WPIntegratorModule The plugin module
	 */
	public function getManager()
	{
		return $this->_pluginModule;
	}

	/**
	 * Sets the plugin module.
	 *
	 * @param WPIntegratorModule $manager The plugin manager
	 * @return void
	 */
	public function setManager($manager)
	{
		$this->_pluginModule = $manager;
	}

	
	/**
	 * Returns a user instance given the user name.
	 * @param null|string $username user name, null if it is a guest.
	 * @return TUser the user instance, null if the specified username is not in the user database.
	 */
	public function getUser($username = null)
	{
		$user = $this->_pluginModule->get_user_by('id', $username);
		return $user;
	}

	/**
	 * Gets a user by its name.
	 *
	 * @param string $name The user name
	 * @return WPUser The user object or null if not found
	 */
	public function getUserByName($name)
	{
		$user = $this->_pluginModule->get_user_by('login', $name);
		return $user;
	}

	/**
	 * Gets a user by its email.
	 *
	 * @param string $email The user email
	 * @return WPUser The user object or null if not found
	 */
	public function getUserByEmail($email)
	{
		$user = $this->_pluginModule->get_user_by('email', $email);
		return $user;
	}

	/**
	 * Validates user credentials.
	 *
	 * @param string $username The username
	 * @param string $password The password
	 * @return bool Whether validation succeeded
	 */
	public function validateUser($username, $password)
	{
		return false;
	}
}

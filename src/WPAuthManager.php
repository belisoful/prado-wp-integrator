<?php

/**
 * WPAuthManager class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator;

require 'composer.php';

use Prado\Security\TAuthManager;
use Prado\Security\TDbUser;

/**
 * WPAuthManager class
 *
 * Authentication manager for WordPress integration.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPAuthManager extends TAuthManager
{
	/**
	 * @var WPIntegratorModule The plugin manager
	 */
	protected WPIntegratorModule $_pluginManager;

	/**
	 * Generates the user key for storing user information in session.
	 *
	 * @return string a key used to store user information in session
	 */
	protected function generateUserKey()
	{
		return LOGGED_IN_COOKIE;
	}

	/**
	 * Returns the plugin manager.
	 * @return null|WPIntegratorModule for the plugin module
	 */
	public function getManager()
	{
		return $this->_pluginManager;
	}

	/**
	 * Sets the plugin manager.
	 * @param WPIntegratorModule $manager sets WPIntegratorModule for the plugin module
	 */
	public function setManager(WPIntegratorModule $manager)
	{
		$this->_pluginManager = $manager;
	}

}

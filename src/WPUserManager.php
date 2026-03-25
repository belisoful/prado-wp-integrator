<?php

namespace PradoWpIntegrator;

use Prado\Security\TDbUserManager;

class WPUserManager extends TDbUserManager
{
	protected $_pluginmanager;
	
	const WP_USER_ROLLS = 'wp_user_roles';
	
	/**
	 * @return TPluginManager for the plugin module
	 */
	public function getManager()
	{
		return $this->_pluginmanager;
	}
	
	
	/**
	 * @param TPluginManage $manager sets TPluginManager for the plugin module
	 */
	public function setManager($manager)
	{
		$this->_pluginmanager=$manager;
	}
	
}
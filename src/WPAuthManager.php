<?php

namespace PradoWpIntegrator;

use Prado\Security\TDbUser;

class WPAuthManager extends TAuthManager
{
	protected $_pluginmanager;
	
	/**
	 * @return string a key used to store user information in session
	 */
	protected function generateUserKey()
	{
		return LOGGED_IN_COOKIE;
	}
	
	
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
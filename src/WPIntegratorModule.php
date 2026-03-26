<?php

/**
 * WPIntegratorModule class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator;

require 'composer.php';

use Prado\Util\TDbPluginModule;
use Prado\Util\TDbParameterModule;
use Prado\TPropertyValue;
use Prado\Security\TDbUser;

/**
 * WPIntegratorModule class
 *
 * Module to integrate WordPress with PRADO framework.
 * Allows using WordPress database for authentication and content management.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPIntegratorModule extends TDbPluginModule
{
	/**
	 * @var string The database prefix used for WordPress tables
	 */
	protected $_databasePrefix = 'wp_';

	/**
	 * @var array The WordPress options cache
	 */
	protected $_options = [];

	/**
	 * @var TDbParameterModule The database parameters module for WordPress
	 */
	protected $_dbParameters;

	/**
	 * @var string The WordPress user manager ID
	 */
	protected $_userManagerId = '';

	/**
	 * @var WPUserManager The WordPress user manager
	 */
	protected $_userManager;

	/**
	 * @var string The WordPress auth manager ID
	 */
	protected $_authManagerId = '';

	/**
	 * @var WPAuthManager The WordPress auth manager
	 */
	protected $_authManager;

	/**
	 * @var string The WordPress database parameter ID
	 */
	protected $_dbParamId = '';

	/**
	 * @var string The WordPress directory path
	 */
	private $_wpDirectory = '';

	/**
	 * @var string The login page path
	 */
	private $_loginPage;

	//	***  Keys  ***
	/**
	 * @var string The WordPress secret key
	 */
	private $_secretKey;

	/**
	 * @var string The WordPress auth key
	 */
	private $_authKey;

	/**
	 * @var string The WordPress secure auth key
	 */
	private $_secureAuthKey;

	/**
	 * @var string The WordPress logged in key
	 */
	private $_loggedInKey;


	//	***  Salt  ***
	/**
	 * @var string The WordPress secret salt
	 */
	private $_secretSalt;

	/**
	 * @var string The WordPress auth salt
	 */
	private $_authSalt;

	/**
	 * @var string The WordPress secure auth salt
	 */
	private $_secureAuthSalt;

	/**
	 * @var string The WordPress logged in salt
	 */
	private $_loggedInSalt;

	/**
	 * @var string The WordPress nonce salt
	 */
	private $_nonceSalt;


	/**
	 * Gets the WordPress secret key.
	 * @return string The secret key
	 */
	public function getSecretKey()
	{
		return $this->_secretKey;
	}

	/**
	 * Sets the WordPress secret key.
	 * @param string $value The secret key
	 */
	public function setSecretKey($value)
	{
		$this->_secretKey = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress auth key.
	 * @return string The auth key
	 */
	public function getAuthKey()
	{
		return $this->_authKey;
	}

	/**
	 * Sets the WordPress auth key.
	 * @param string $value The auth key
	 */
	public function setAuthKey($value)
	{
		$this->_authKey = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress secure auth key.
	 * @return string The secure auth key
	 */
	public function getSecureAuthKey()
	{
		return $this->_secureAuthKey;
	}

	/**
	 * Sets the WordPress secure auth key.
	 * @param string $value The secure auth key
	 */
	public function setSecureAuthKey($value)
	{
		$this->_secureAuthKey = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress logged in key.
	 * @return string The logged in key
	 */
	public function getLoggedInKey()
	{
		return $this->_loggedInKey;
	}

	/**
	 * Sets the WordPress logged in key.
	 * @param string $value The logged in key
	 */
	public function setLoggedInKey($value)
	{
		$this->_loggedInKey = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress secret salt.
	 * @return string The secret salt
	 */
	public function getSecretSalt()
	{
		return $this->_secretSalt;
	}

	/**
	 * Sets the WordPress secret salt.
	 * @param string $value The secret salt
	 */
	public function setSecretSalt($value)
	{
		$this->_secretSalt = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress auth salt.
	 * @return string The auth salt
	 */
	public function getAuthSalt()
	{
		return $this->_authSalt;
	}

	/**
	 * Sets the WordPress auth salt.
	 * @param string $value The auth salt
	 */
	public function setAuthSalt($value)
	{
		$this->_authSalt = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress secure auth salt.
	 * @return string The secure auth salt
	 */
	public function getSecureAuthSalt()
	{
		return $this->_secureAuthSalt;
	}

	/**
	 * Sets the WordPress secure auth salt.
	 * @param string $value The secure auth salt
	 */
	public function setSecureAuthSalt($value)
	{
		$this->_secureAuthSalt = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress logged in salt.
	 * @return string The logged in salt
	 */
	public function getLoggedInSalt()
	{
		return $this->_loggedInSalt;
	}

	/**
	 * Sets the WordPress logged in salt.
	 * @param string $value The logged in salt
	 */
	public function setLoggedInSalt($value)
	{
		$this->_loggedInSalt = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress nonce salt.
	 * @return string The nonce salt
	 */
	public function getNonceSalt()
	{
		return $this->_nonceSalt;
	}

	/**
	 * Sets the WordPress nonce salt.
	 * @param string $value The nonce salt
	 */
	public function setNonceSalt($value)
	{
		$this->_nonceSalt = TPropertyValue::ensureString($value);
	}


	public function dyPreInit($config)
	{
		// Load the WordPress Parameters
		$this->_dbParameters = new TDbParameterModule();
		$this->_dbParameters->setId($this->getWPDbParameterID());
		$this->_dbParameters->setConnectionId($this->getConnectionID());
		$this->_dbParameters->setKeyField('option_name');
		$this->_dbParameters->setValueField('option_value');
		$this->_dbParameters->setTableName($this->_databasePrefix . 'options');
		$this->_dbParameters->setAutoLoadField('autoload');
		$this->_dbParameters->setAutoLoadValue('\'yes\'');
		$this->_dbParameters->setAutoLoadValueFalse('\'no\'');
		$this->getApplication()->setModule($this->getWPDbParameterID(), $this->_dbParameters);
		$this->_dbParameters->dyPreInit(null);

		// Load the Wordpress User Manager
		//var_dump(unserialize($this->getOption(self::WP_USER_ROLLS)));
		$this->_userManager = new WPUserManager();
		$this->_userManager->setId($this->getWPUserManagerID());
		$this->_userManager->setPluginModule($this);
		$this->_userManager->setUserClass('WPUser');
		$this->getApplication()->setModule($this->getWPUserManagerID(), $this->_userManager);
		$this->_userManager->dyPreInit(null);

		// Load the Wordpress Auth Manager
		$this->_authManager = new WPAuthManager();
		$this->_authManager->setId($this->getWPAuthManagerID());
		$this->_authManager->setPluginModule($this);
		$this->_authManager->setUserManager($this->getWPUserManagerID());
		$this->_authManager->setAllowAutoLogin(true);
		$this->_authManager->setLoginPage($this->_loginPage);
		$this->getApplication()->setModule($this->getWPAuthManagerID(), $this->_authManager);
		$this->_authManager->dyPreInit(null);
	}

	public function init($config)
	{
		$this->_dbParameters->init(null);
		parent::init($config);

		$this->initializeWPConstants();

		$this->_userManager->init(null);
		$this->_authManager->init(null);
	}

	/*
	protected function autoloadOptions()
	{
		$connection = $this->getDbConnection();
		$cmd = $connection->createCommand(
			"SELECT option_name, option_value FROM {$this->_databasePrefix}options WHERE autoload='yes'" );
		$results = $cmd->query();
		$all = $results->readAll();

		$appParameters = $this->getApplication()->getParameters();
		foreach($all as $row) {
			$this->_options[$row['option_name']] = $row['option_value'];
			$appParameters[$row['option_name']] = $row['option_value'];
		}
	}*/


	/**
	 * This replicates the Wordpress function get_site_option
	 * @param mixed $key
	 */
	public function get_site_option($key)
	{
		if (isset($this->_options[$key])) {
			return $this->_options[$key];
		}

		$parameters = $this->getApplication()->getParameters();
		if (isset($parameters[$key])) {
			return $parameters[$key];
		}

		$connection = $this->getDbConnection();
		$cmd = $connection->createCommand(
			"SELECT option_value FROM {$this->_databasePrefix}options WHERE option_name=:name LIMIT 1"
		);
		$cmd->bindParameter(":name", $key, \PDO::PARAM_STR);
		$value = $cmd->queryRow();
		$this->_options[$key] = $value['option_value'];
		return $value['option_value'];
	}


	/**
	 * This replicates the Wordpress function get_site_option
	 * @param mixed $field
	 * @param mixed $value
	 */
	public function get_user_by($field, $value)
	{
		if ($field == 'id') {
			$field = "ID";
		} elseif ($field == 'email') {
			$field = 'user_email';
		} elseif ($field == 'login') {
			$field = 'user_login';
		} else {
			return null;
		}

		$connection = $this->getDbConnection();
		$cmd = $connection->createCommand(
			"SELECT * FROM {$this->_databasePrefix}users WHERE {$field}=:value LIMIT 1"
		);
		$cmd->bindParameter(":value", $value, \PDO::PARAM_STR);
		$userFields = $cmd->queryRow();

		if (!isset($userFields['ID']) && !$userFields['ID']) {
			return null;
		}

		$cmd = $connection->createCommand(
			"SELECT meta_key, meta_value FROM {$this->_databasePrefix}usermeta WHERE user_id=:id"
		);
		$cmd->bindParameter(":id", $userFields['ID'], \PDO::PARAM_INT);
		$results = $cmd->query();
		$meta = [];

		foreach ($results->readAll() as $kv) {
			$meta[$kv['meta_key']] = $kv['meta_value'];
		}

		$user = new WPUser($this->_userManager);
		$user->load($userFields, $meta);
		return $user;
	}

	public function wp_hash($data, $scheme = 'auth')
	{
		$salt = $this->wp_salt($scheme);

		return hash_hmac('md5', $data, $salt);
	}
	public function wp_salt($scheme = 'auth')
	{
		static $cached_salts = [];
		if (isset($cached_salts[ $scheme ])) {
			/**
			 * Filters the WordPress salt.
			 *
			 * @since 2.5.0
			 *
			 * @param string $cached_salt Cached salt for the given scheme.
			 * @param string $scheme      Authentication scheme. Values include 'auth',
			 *                            'secure_auth', 'logged_in', and 'nonce'.
			 */
			return apply_filters('salt', $cached_salts[ $scheme ], $scheme);
		}

		static $duplicated_keys;
		if (null === $duplicated_keys) {
			$duplicated_keys = ['put your unique phrase here' => true];
			foreach (['AUTH', 'SECURE_AUTH', 'LOGGED_IN', 'NONCE', 'SECRET'] as $first) {
				foreach (['KEY', 'SALT'] as $second) {
					if (! defined("{$first}_{$second}")) {
						continue;
					}
					$value = constant("{$first}_{$second}");
					$duplicated_keys[ $value ] = isset($duplicated_keys[ $value ]);
				}
			}
		}

		$values = [
			'key' => '',
			'salt' => '',
		];
		if (defined('SECRET_KEY') && SECRET_KEY && empty($duplicated_keys[ SECRET_KEY ])) {
			$values['key'] = SECRET_KEY;
		}
		if ('auth' === $scheme && defined('SECRET_SALT') && SECRET_SALT && empty($duplicated_keys[ SECRET_SALT ])) {
			$values['salt'] = SECRET_SALT;
		}

		if (in_array($scheme, ['auth', 'secure_auth', 'logged_in', 'nonce'], true)) {
			foreach (['key', 'salt'] as $type) {
				$const = strtoupper("{$scheme}_{$type}");
				if (defined($const) && constant($const) && empty($duplicated_keys[ constant($const) ])) {
					$values[ $type ] = constant($const);
				} elseif (! $values[ $type ]) {
					$values[ $type ] = $this->get_site_option("{$scheme}_{$type}");
					if (! $values[ $type ]) {
						// error
					}
				}
			}
		} else {
			if (! $values['key']) {
				$values['key'] = $this->get_site_option('secret_key');
				if (! $values[ 'key' ]) {
					// error
				}
			}
			$values['salt'] = hash_hmac('md5', $scheme, $values['key']);
		}

		$cached_salts[ $scheme ] = $values['key'] . $values['salt'];

		/** This filter is documented in wp-includes/pluggable.php */
		return $cached_salts[ $scheme ];
	}

	/** @var array The post cache */
	private $_postCache = [];

	/**
	 * Gets a WordPress post by ID.
	 * @param int $postId The post ID
	 * @return WPPost The post object
	 */
	public function getWPPost($postId)
	{
		if (isset($this->_postCache[$postId])) {
			return $this->_postCache[$postId];
		}
		$connection = $this->getDbConnection();
		$cmd = $connection->createCommand(
			"SELECT * FROM {$this->_databasePrefix}posts WHERE ID=:id LIMIT 1"
		);
		$cmd->bindParameter(":id", $postId, \PDO::PARAM_INT);
		$post = $cmd->queryRow();

		$cmd = $connection->createCommand(
			"SELECT meta_key, meta_value FROM {$this->_databasePrefix}postmeta WHERE post_id=:id"
		);
		$cmd->bindParameter(":id", $postId, \PDO::PARAM_INT);
		$results = $cmd->query();
		$meta = [];

		foreach ($results->readAll() as $kv) {
			$meta[$kv['meta_key']] = $kv['meta_value'];
		}
		$post = new WPPost($post, $meta);
		$this->_postCache[$postId] = $post;
		return $post;
	}

	/**
	 * Gets the database prefix.
	 * @return string The database prefix
	 */
	public function getDatabasePrefx()
	{
		return $this->_databasePrefix;
	}

	/**
	 * Sets the database prefix.
	 * @param string $prefix The database prefix
	 */
	public function setDatabasePrefx($prefix)
	{
		$this->_databasePrefix = TPropertyValue::ensureString($prefix);
	}

	/**
	 * Gets the WordPress user manager ID.
	 * @return string The user manager ID
	 */
	public function getWPUserManagerID()
	{
		return $this->_userManagerId;
	}

	/**
	 * Sets the WordPress user manager ID.
	 * @param string $userManagerId The user manager ID
	 */
	public function setWPUserManagerID($userManagerId)
	{
		$this->_userManagerId = TPropertyValue::ensureString($userManagerId);
	}

	/**
	 * Gets the WordPress auth manager ID.
	 * @return string The auth manager ID
	 */
	public function getWPAuthManagerID()
	{
		return $this->_authManagerId;
	}

	/**
	 * Sets the WordPress auth manager ID.
	 * @param string $authManagerId The auth manager ID
	 */
	public function setWPAuthManagerID($authManagerId)
	{
		$this->_authManagerId = TPropertyValue::ensureString($authManagerId);
	}

	/**
	 * Gets the WordPress database parameter ID.
	 * @return string The database parameter ID
	 */
	public function getWPDbParameterID()
	{
		return $this->_dbParamId;
	}

	/**
	 * Sets the WordPress database parameter ID.
	 * @param string $value The database parameter ID
	 */
	public function setWPDbParameterID($value)
	{
		$this->_dbParamId = TPropertyValue::ensureString($value);
	}

	/**
	 * Gets the WordPress directory.
	 * @return string The WordPress directory
	 */
	public function getWPDirectory()
	{
		return $this->_wpDirectory;
	}

	/**
	 * Sets the WordPress directory.
	 * @param string $value The WordPress directory
	 */
	public function setWPDirectory($value)
	{
		$this->_wpDirectory = TPropertyValue::ensureString($value);
	}

	/**
	 * Returns the login page path.
	 * @return string path of login page should login is required
	 */
	public function getLoginPage()
	{
		return $this->_loginPage;
	}

	/**
	 * Sets the login page that the client browser will be redirected to if login is needed.
	 * Login page should be specified in the format of page path.
	 * @param string $pagePath path of login page should login is required
	 * @see TPageService
	 */
	public function setLoginPage($pagePath)
	{
		$this->_loginPage = $pagePath;
	}

	/**
	 * Initializes the WordPress constants.
	 */
	protected function initializeWPConstants()
	{
		$parameters = $this->getApplication()->getParameters();
		// From wp-includes/default-constants.php - #223 function wp_cookie_constants()
		/**
		 * Used to guarantee unique hash cookies.
		 *
		 * @since 1.5.0
		 */
		if (! defined('COOKIEHASH')) {
			$siteurl = $this->get_site_option('siteurl');
			if ($siteurl) {
				define('COOKIEHASH', md5($siteurl));
			} else {
				define('COOKIEHASH', '');
			}
		}

		/**
		 * @since 2.0.0
		 */
		if (! defined('USER_COOKIE')) {
			define('USER_COOKIE', 'wordpressuser_' . COOKIEHASH);
		}

		/**
		 * @since 2.0.0
		 */
		if (! defined('PASS_COOKIE')) {
			define('PASS_COOKIE', 'wordpresspass_' . COOKIEHASH);
		}

		/**
		 * @since 2.5.0
		 */
		if (! defined('AUTH_COOKIE')) {
			define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);
		}

		/**
		 * @since 2.6.0
		 */
		if (! defined('SECURE_AUTH_COOKIE')) {
			define('SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH);
		}

		/**
		 * @since 2.6.0
		 */
		if (! defined('LOGGED_IN_COOKIE')) {
			define('LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH);
		}

		/**
		 * @since 2.3.0
		 */
		if (! defined('TEST_COOKIE')) {
			define('TEST_COOKIE', 'wordpress_test_cookie');
		}

		/**
		 * @since 1.2.0
		 */
		if (! defined('COOKIEPATH')) {
			define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', $this->get_site_option('home') . '/'));
		}

		/**
		 * @since 1.5.0
		 */
		if (! defined('SITECOOKIEPATH')) {
			define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', $this->get_site_option('siteurl') . '/'));
		}

		/**
		 * @since 2.6.0
		 */
		if (! defined('ADMIN_COOKIE_PATH')) {
			define('ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin');
		}

		/**
		 * @since 2.6.0
		 */
		if (! defined('PLUGINS_COOKIE_PATH') && defined('WP_PLUGIN_URL')) {
			define('PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL));
		}

		/**
		 * @since 2.0.0
		 */
		if (! defined('COOKIE_DOMAIN')) {
			define('COOKIE_DOMAIN', false);
		}

		if (! defined('RECOVERY_MODE_COOKIE')) {
			/**
			 * @since 5.2.0
			 */
			define('RECOVERY_MODE_COOKIE', 'wordpress_rec_' . COOKIEHASH);
		}

		//From wp-config.php
		if (! defined('SECRET_KEY')) {
			define('SECRET_KEY', $this->getSecretKey());
		}
		if (! defined('AUTH_KEY')) {
			define('AUTH_KEY', $this->getAuthKey());
		}
		if (! defined('SECURE_AUTH_KEY')) {
			define('SECURE_AUTH_KEY', $this->getSecureAuthKey());
		}
		if (! defined('LOGGED_IN_KEY')) {
			define('LOGGED_IN_KEY', $this->getLoggedInKey());
		}
		if (! defined('NONCE_KEY')) {
			define('NONCE_KEY', $this->getLoggedInKey());
		}
		if (! defined('SECRET_SALT')) {
			define('SECRET_SALT', $this->getSecretSalt());
		}
		if (! defined('AUTH_SALT')) {
			define('AUTH_SALT', $this->getAuthSalt());
		}
		if (! defined('SECURE_AUTH_SALT')) {
			define('SECURE_AUTH_SALT', $this->getSecureAuthSalt());
		}
		if (! defined('LOGGED_IN_SALT')) {
			define('LOGGED_IN_SALT', $this->getLoggedInSalt());
		}
		if (! defined('NONCE_SALT')) {
			define('NONCE_SALT', $this->getNonceSalt());
		}
	}

}

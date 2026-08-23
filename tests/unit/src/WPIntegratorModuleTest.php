<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\WPAuthManager;
use PradoWpIntegrator\WPIntegratorModule;
use PradoWpIntegrator\WPPost;
use PradoWpIntegrator\WPUser;
use PradoWpIntegrator\WPUserManager;

/**
 * Test class for WPIntegratorModule.
 *
 * Anything that reaches init() runs in its own process: initializeWPConstants()
 * defines WordPress' cookie and key constants, and wp_salt() caches salts in a
 * static, neither of which can be undone within a process.
 */
class WPIntegratorModuleTest extends WPTestCase
{
    public function testGetSetDatabasePrefix()
    {
        $module = new WPIntegratorModule();
        $module->setDatabasePrefx('wp_test_');
        $this->assertSame('wp_test_', $module->getDatabasePrefx());
    }

    public function testGetSetWPUserManagerID()
    {
        $module = new WPIntegratorModule();
        $module->setWPUserManagerID('testUserManager');
        $this->assertSame('testUserManager', $module->getWPUserManagerID());
    }

    public function testGetSetWPAuthManagerID()
    {
        $module = new WPIntegratorModule();
        $module->setWPAuthManagerID('testAuthManager');
        $this->assertSame('testAuthManager', $module->getWPAuthManagerID());
    }

    public function testGetSetWPDbParameterID()
    {
        $module = new WPIntegratorModule();
        $module->setWPDbParameterID('testDbParameter');
        $this->assertSame('testDbParameter', $module->getWPDbParameterID());
    }

    public function testGetSetWPDirectory()
    {
        $module = new WPIntegratorModule();
        $module->setWPDirectory('/path/to/wordpress');
        $this->assertSame('/path/to/wordpress', $module->getWPDirectory());
    }

    public function testGetSetLoginPage()
    {
        $module = new WPIntegratorModule();
        $module->setLoginPage('Login');
        $this->assertSame('Login', $module->getLoginPage());
    }

    /**
     * Every key and salt property, which are the values fed to the WordPress
     * key constants by initializeWPConstants().
     *
     * @return array<string, array{0: string}> the property names
     */
    public static function keyAndSaltPropertyProvider(): array
    {
        $properties = [
            'SecretKey', 'AuthKey', 'SecureAuthKey', 'LoggedInKey',
            'SecretSalt', 'AuthSalt', 'SecureAuthSalt', 'LoggedInSalt', 'NonceSalt',
        ];
        $cases = [];
        foreach ($properties as $property) {
            $cases[$property] = [$property];
        }
        return $cases;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('keyAndSaltPropertyProvider')]
    public function testGetSetKeysAndSalts(string $property)
    {
        $module = new WPIntegratorModule();
        $this->assertNull($module->{'get' . $property}());

        $module->{'set' . $property}('value-of-' . $property);
        $this->assertSame('value-of-' . $property, $module->{'get' . $property}());
    }

    public function testKeysAndSaltsAreCastToString()
    {
        $module = new WPIntegratorModule();
        $module->setSecretKey(123);
        $this->assertSame('123', $module->getSecretKey());
    }

    // ---------------------------------------------------------------- options

    public function testGetSiteOptionReadsTheDatabase()
    {
        $module = $this->createModule();

        $this->assertSame('http://example.com', $module->get_site_option('siteurl'));
    }

    public function testGetSiteOptionCachesTheValue()
    {
        $module = $this->createModule();
        $this->assertSame('http://example.com', $module->get_site_option('siteurl'));

        // the cached value is returned even after the row changes
        $this->execute("UPDATE wp_options SET option_value='http://changed.example' WHERE option_name='siteurl'");
        $this->assertSame('http://example.com', $module->get_site_option('siteurl'));
    }

    public function testGetSiteOptionPrefersApplicationParameters()
    {
        $module = $this->createModule();
        $this->app->getParameters()->add('siteurl', 'http://parameter.example');

        $this->assertSame('http://parameter.example', $module->get_site_option('siteurl'));
    }

    public function testGetSiteOptionReturnsNullForAnUnknownOption()
    {
        $module = $this->createModule();

        $this->assertNull($module->get_site_option('no_such_option'));
    }

    // ------------------------------------------------------------------ users

    public function testGetUserByLogin()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);

        $user = $module->get_user_by('login', 'alice');

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice', $user->user_login);
        $this->assertSame('alice@example.com', $user->user_email);
        // meta is merged in behind the columns
        $this->assertSame('Alice', $user->first_name);
    }

    public function testGetUserById()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);

        $user = $module->get_user_by('id', 1);

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice', $user->user_login);
    }

    public function testGetUserByEmail()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);

        $user = $module->get_user_by('email', 'alice@example.com');

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice', $user->user_login);
    }

    public function testGetUserByRejectsAnUnknownField()
    {
        $module = $this->createModule();

        $this->assertNull($module->get_user_by('nickname', 'alice'));
    }

    public function testGetUserByReturnsNullWhenThereIsNoSuchUser()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);

        $this->assertNull($module->get_user_by('login', 'nobody'));
    }

    // ------------------------------------------------------------------ posts

    public function testGetWPPostReadsPostAndMeta()
    {
        $module = $this->createModule();

        $post = $module->getWPPost(1);

        $this->assertInstanceOf(WPPost::class, $post);
        $this->assertSame('Hello', $post->getTitle());
        $this->assertSame('<p>Hello world</p>', $post->getContent());
        $this->assertSame('yes', $post->getMeta('thumbnail'));
    }

    public function testGetWPPostCachesByPostId()
    {
        $module = $this->createModule();
        $post = $module->getWPPost(1);

        $this->assertSame($post, $module->getWPPost(1));
    }

    // ----------------------------------------------------------------- wiring

    public function testDyPreInitRegistersTheSubModules()
    {
        $module = $this->createModule();

        $module->dyPreInit(null);

        $this->assertInstanceOf(WPUserManager::class, $this->app->getModule('wpusermanager'));
        $this->assertInstanceOf(WPAuthManager::class, $this->app->getModule('wpauthmanager'));
        $this->assertNotNull($this->app->getModule('wpdbparameter'));
        $this->assertSame($module, $this->app->getModule('wpusermanager')->getPluginModule());
        $this->assertSame($module, $this->app->getModule('wpauthmanager')->getPluginModule());
        $this->assertTrue($this->app->getModule('wpauthmanager')->getAllowAutoLogin());
    }

    public function testDyPreInitPassesTheLoginPageToTheAuthManager()
    {
        $module = $this->createModule(['LoginPage' => 'Users.Login']);

        $module->dyPreInit(null);

        $this->assertSame('Users.Login', $this->app->getModule('wpauthmanager')->getLoginPage());
    }

    public function testTheUserManagerBuildsWPUsers()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);
        $userManager = $this->app->getModule('wpusermanager');
        $userManager->init(null);

        // the module points the user manager at this package's user class;
        // TDbUserManager::init() fails outright if the class cannot be created
        $this->assertSame(WPUser::class, $userManager->getUserClass());
        $this->assertInstanceOf(WPUser::class, $userManager->getUserByName('alice'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInitDefinesTheWordPressCookieConstants()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);

        $module->init(null);

        $this->assertSame(md5('http://example.com'), COOKIEHASH);
        $this->assertSame('wordpressuser_' . COOKIEHASH, USER_COOKIE);
        $this->assertSame('wordpresspass_' . COOKIEHASH, PASS_COOKIE);
        $this->assertSame('wordpress_' . COOKIEHASH, AUTH_COOKIE);
        $this->assertSame('wordpress_sec_' . COOKIEHASH, SECURE_AUTH_COOKIE);
        $this->assertSame('wordpress_logged_in_' . COOKIEHASH, LOGGED_IN_COOKIE);
        $this->assertSame('wordpress_test_cookie', TEST_COOKIE);
        $this->assertSame('wordpress_rec_' . COOKIEHASH, RECOVERY_MODE_COOKIE);
        $this->assertSame('/', COOKIEPATH);
        $this->assertSame('/', SITECOOKIEPATH);
        $this->assertSame('/wp-admin', ADMIN_COOKIE_PATH);
        $this->assertFalse(COOKIE_DOMAIN);
        $this->assertFalse(defined('PLUGINS_COOKIE_PATH'), 'no WP_PLUGIN_URL, so no plugin cookie path');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInitDefinesAnEmptyCookieHashWithoutASiteUrl()
    {
        $this->execute("DELETE FROM wp_options WHERE option_name='siteurl'");
        $module = $this->createModule();
        $module->dyPreInit(null);

        $module->init(null);

        $this->assertSame('', COOKIEHASH);
        $this->assertSame('wordpressuser_', USER_COOKIE);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInitDefinesThePluginCookiePathWhenWordPressHasAPluginUrl()
    {
        define('WP_PLUGIN_URL', 'http://example.com/wp-content/plugins');
        $module = $this->createModule();
        $module->dyPreInit(null);

        $module->init(null);

        $this->assertSame('/wp-content/plugins', PLUGINS_COOKIE_PATH);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInitDefinesTheKeyAndSaltConstantsFromTheModule()
    {
        $module = $this->createModule([
            'SecretKey' => 'secret-key',
            'AuthKey' => 'auth-key',
            'SecureAuthKey' => 'secure-auth-key',
            'LoggedInKey' => 'logged-in-key',
            'SecretSalt' => 'secret-salt',
            'AuthSalt' => 'auth-salt',
            'SecureAuthSalt' => 'secure-auth-salt',
            'LoggedInSalt' => 'logged-in-salt',
            'NonceSalt' => 'nonce-salt',
        ]);
        $module->dyPreInit(null);

        $module->init(null);

        $this->assertSame('secret-key', SECRET_KEY);
        $this->assertSame('auth-key', AUTH_KEY);
        $this->assertSame('secure-auth-key', SECURE_AUTH_KEY);
        $this->assertSame('logged-in-key', LOGGED_IN_KEY);
        // NONCE_KEY is taken from the logged-in key
        $this->assertSame('logged-in-key', NONCE_KEY);
        $this->assertSame('secret-salt', SECRET_SALT);
        $this->assertSame('auth-salt', AUTH_SALT);
        $this->assertSame('secure-auth-salt', SECURE_AUTH_SALT);
        $this->assertSame('logged-in-salt', LOGGED_IN_SALT);
        $this->assertSame('nonce-salt', NONCE_SALT);
    }

    // ------------------------------------------------------------ salts/hashes

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltUsesTheConfiguredKeysAndCachesThem()
    {
        $module = $this->createModule([
            'SecretKey' => 'secret-key',
            'SecretSalt' => 'secret-salt',
            'AuthKey' => 'auth-key',
            'AuthSalt' => 'auth-salt',
        ]);
        $module->dyPreInit(null);
        $module->init(null);

        $salt = $module->wp_salt('auth');

        $this->assertSame('auth-keyauth-salt', $salt);
        // the second call comes out of the static cache
        $this->assertSame($salt, $module->wp_salt('auth'));
        // and wp_hash is an hmac over it
        $this->assertSame(hash_hmac('md5', 'data', $salt), $module->wp_hash('data'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltFallsBackToTheOptionsTableWhenNoKeysAreConfigured()
    {
        $this->addOption('logged_in_key', 'option-key');
        $this->addOption('logged_in_salt', 'option-salt');
        $module = $this->createModule();
        $module->dyPreInit(null);
        $module->init(null);

        $this->assertSame('option-keyoption-salt', $module->wp_salt('logged_in'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltOfAnUnknownSchemeIsDerivedFromTheSecretKey()
    {
        $this->addOption('secret_key', 'option-secret');
        $module = $this->createModule();
        $module->dyPreInit(null);
        $module->init(null);

        $expected = 'option-secret' . hash_hmac('md5', 'custom', 'option-secret');
        $this->assertSame($expected, $module->wp_salt('custom'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltIgnoresKeysThatAreUsedMoreThanOnce()
    {
        // the same value in two constants is WordPress' "duplicated key" case:
        // it must not be used as a salt
        $module = $this->createModule([
            'SecretKey' => 'shared-value',
            'AuthKey' => 'shared-value',
            'SecretSalt' => 'shared-value',
            'AuthSalt' => 'shared-value',
        ]);
        $this->addOption('auth_key', 'option-auth-key');
        $this->addOption('auth_salt', 'option-auth-salt');
        $module->dyPreInit(null);
        $module->init(null);

        $this->assertSame('option-auth-keyoption-auth-salt', $module->wp_salt('auth'));
    }

    /**
     * wp_salt() on a module that was never initialized: none of the WordPress
     * key constants exist, so every one of them is skipped and the values come
     * from the options table.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltWithoutAnyKeyConstantsDefined()
    {
        $this->addOption('auth_key', 'option-auth-key');
        $this->addOption('auth_salt', 'option-auth-salt');
        $module = $this->createModule();

        $this->assertFalse(defined('AUTH_KEY'), 'the module was not initialized');
        $this->assertSame('option-auth-keyoption-auth-salt', $module->wp_salt('auth'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testSaltTolueratesMissingKeyOptions()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);
        $module->init(null);

        // nothing is configured and nothing is in the options table
        $this->assertSame('', $module->wp_salt('nonce'));
    }
}

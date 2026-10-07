<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Prado\Security\TAuthManager;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\WPAuthManager;

/**
 * Test class for WPAuthManager.
 */
class WPAuthManagerTest extends WPTestCase
{
    public function testConstructor()
    {
        $authManager = new WPAuthManager();

        $this->assertInstanceOf(WPAuthManager::class, $authManager);
        $this->assertInstanceOf(TAuthManager::class, $authManager);
    }

    public function testGetSetPluginModule()
    {
        $authManager = new WPAuthManager();
        $module = $this->createModule();

        $authManager->setPluginModule($module);

        $this->assertSame($module, $authManager->getPluginModule());
    }

    /**
     * The session key is WordPress' logged-in cookie name, so the user is
     * shared with WordPress rather than kept under PRADO's own key.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testTheSessionKeyIsTheWordPressLoggedInCookie()
    {
        $module = $this->createModule();
        $module->dyPreInit(null);
        $module->init(null);
        $authManager = $this->app->getModule('wpauthmanager');

        $method = new \ReflectionMethod($authManager, 'generateUserKey');

        $this->assertSame(LOGGED_IN_COOKIE, $method->invoke($authManager));
        $this->assertSame('wordpress_logged_in_' . md5('http://example.com'), $method->invoke($authManager));
    }
}

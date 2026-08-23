<?php

namespace PradoWpIntegrator\Test;

use Prado\Security\TDbUserManager;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\WPUser;
use PradoWpIntegrator\WPUserManager;

/**
 * Test class for WPUserManager.
 */
class WPUserManagerTest extends WPTestCase
{
    public function testConstructor()
    {
        $userManager = new WPUserManager();

        $this->assertInstanceOf(WPUserManager::class, $userManager);
        $this->assertInstanceOf(TDbUserManager::class, $userManager);
    }

    public function testGetSetPluginModule()
    {
        $userManager = new WPUserManager();
        $module = $this->createModule();

        $userManager->setPluginModule($module);

        $this->assertSame($module, $userManager->getPluginModule());
    }

    public function testPluginModuleIsNullUntilItIsSet()
    {
        $this->assertNull((new WPUserManager())->getPluginModule());
    }

    public function testWpUserRolesConstant()
    {
        $this->assertSame('wp_user_roles', WPUserManager::WP_USER_ROLLS);
    }

    /**
     * @return WPUserManager a manager wired to a module on the test database
     */
    private function wiredManager(): WPUserManager
    {
        $module = $this->createModule();
        $module->dyPreInit(null);
        return $this->app->getModule('wpusermanager');
    }

    public function testGetUserLooksUpById()
    {
        $user = $this->wiredManager()->getUser(1);

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice', $user->user_login);
    }

    public function testGetUserReturnsNullForAnUnknownId()
    {
        $this->assertNull($this->wiredManager()->getUser(999));
    }

    public function testGetUserByName()
    {
        $user = $this->wiredManager()->getUserByName('alice');

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice@example.com', $user->user_email);
    }

    public function testGetUserByNameReturnsNullForAnUnknownName()
    {
        $this->assertNull($this->wiredManager()->getUserByName('nobody'));
    }

    public function testGetUserByEmail()
    {
        $user = $this->wiredManager()->getUserByEmail('alice@example.com');

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertSame('alice', $user->user_login);
    }

    public function testGetUserByEmailReturnsNullForAnUnknownEmail()
    {
        $this->assertNull($this->wiredManager()->getUserByEmail('nobody@example.com'));
    }

    public function testValidateUserIsAStub()
    {
        $this->assertFalse($this->wiredManager()->validateUser('alice', 'secret'));
    }
}

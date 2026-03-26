<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPUserManager;
use PradoWpIntegrator\WPIntegratorModule;

/**
 * Test class for WPUserManager
 */
class WPUserManagerTest extends TestCase
{
    public function testConstructor()
    {
        $userManager = new WPUserManager();
        $this->assertInstanceOf(WPUserManager::class, $userManager);
    }

    public function testGetSetManager()
    {
        $userManager = new WPUserManager();
        $module = $this->createMock(WPIntegratorModule::class);
        
        $userManager->setPluginModule($module);
        $this->assertEquals($module, $userManager->getPluginModule());
    }
}
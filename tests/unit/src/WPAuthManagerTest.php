<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPAuthManager;
use PradoWpIntegrator\WPIntegratorModule;

/**
 * Test class for WPAuthManager
 */
class WPAuthManagerTest extends TestCase
{
    public function testConstructor()
    {
        $authManager = new WPAuthManager();
        $this->assertInstanceOf(WPAuthManager::class, $authManager);
    }

    public function testGetSetManager()
    {
        $authManager = new WPAuthManager();
        $module = $this->createMock(WPIntegratorModule::class);
        
        $authManager->setPluginModule($module);
        $this->assertEquals($module, $authManager->getPluginModule());
    }
}
<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPIntegratorModule;
use Prado\TPropertyValue;
use Prado\Collections\TComponent;

/**
 * Test class for WPIntegratorModule
 */
class WPIntegratorModuleTest extends TestCase
{
    public function testGetSetDatabasePrefix()
    {
        $module = new WPIntegratorModule();
        $prefix = 'wp_test_';
        $module->setDatabasePrefx($prefix);
        $this->assertEquals($prefix, $module->getDatabasePrefx());
    }

    public function testGetSetWPUserManagerID()
    {
        $module = new WPIntegratorModule();
        $id = 'testUserManager';
        $module->setWPUserManagerID($id);
        $this->assertEquals($id, $module->getWPUserManagerID());
    }

    public function testGetSetWPAuthManagerID()
    {
        $module = new WPIntegratorModule();
        $id = 'testAuthManager';
        $module->setWPAuthManagerID($id);
        $this->assertEquals($id, $module->getWPAuthManagerID());
    }

    public function testGetSetWPDbParameterID()
    {
        $module = new WPIntegratorModule();
        $id = 'testDbParameter';
        $module->setWPDbParameterID($id);
        $this->assertEquals($id, $module->getWPDbParameterID());
    }

    public function testGetSetWPDirectory()
    {
        $module = new WPIntegratorModule();
        $dir = '/path/to/wordpress';
        $module->setWPDirectory($dir);
        $this->assertEquals($dir, $module->getWPDirectory());
    }

    public function testGetSetLoginPage()
    {
        $module = new WPIntegratorModule();
        $page = '/login';
        $module->setLoginPage($page);
        $this->assertEquals($page, $module->getLoginPage());
    }
}
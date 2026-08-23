<?php

namespace PradoWpIntegrator\Test\Theme;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Prado\Util\TPluginModule;
use Prado\Web\Services\TPageService;
use Prado\Web\UI\TPage;
use Prado\Web\UI\TTheme;
use PradoWpIntegrator\TestTools\StubThemedPage;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\Theme\WPThemeBehavior;
use PradoWpIntegrator\Theme\WPThemeMasterClassLayout;
use PradoWpIntegrator\Theme\WPThemeModule;

/**
 * Test class for WPThemeModule.
 *
 * init() installs a class behavior on TTheme, which is global state, so the
 * tests that call it run in their own process.
 */
class WPThemeModuleTest extends WPTestCase
{
    public function testConstructor()
    {
        $module = new WPThemeModule();

        $this->assertInstanceOf(WPThemeModule::class, $module);
        $this->assertInstanceOf(TPluginModule::class, $module);
    }

    public function testBehaviorName()
    {
        $this->assertSame('WPThemer', WPThemeModule::BEHAVIOR_NAME);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInitAttachesTheThemeBehaviorToEveryTheme()
    {
        $module = new WPThemeModule();
        $module->setId('wptheme');
        $this->app->setModule('wptheme', $module);

        $module->init(null);

        $theme = new TTheme($this->themePath(), 'http://example.com/theme');
        $this->assertInstanceOf(WPThemeBehavior::class, $theme->asa(WPThemeModule::BEHAVIOR_NAME));
        // the behavior's methods are now callable on any theme
        $this->assertTrue($theme->isWordPressTheme());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAttachServiceHandlerSubscribesToThePageService()
    {
        $module = new WPThemeModule();
        $module->setId('wptheme');
        $this->app->setModule('wptheme', $module);
        $service = new TPageService();
        $service->setId('page');
        $this->app->setService($service);

        $module->attachServiceHandler($this->app, null);

        $this->assertNotEmpty($service->getEventHandlers('onPreRunPage'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAttachServiceHandlerIgnoresOtherServices()
    {
        $module = new WPThemeModule();
        $module->setId('wptheme');
        $this->app->setModule('wptheme', $module);
        $service = new \Prado\Web\Services\TFeedService();
        $service->setId('feed');
        $this->app->setService($service);

        $module->attachServiceHandler($this->app, null);

        $this->assertFalse($service->hasEvent('onPreRunPage'));
    }

    public function testAttachPageHandlerSubscribesToThePage()
    {
        $module = new WPThemeModule();
        $page = new TPage();

        $module->attachPageHandler(null, $page);

        $this->assertNotEmpty($page->getEventHandlers('onPreInit'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInstallWPThemeSwapsTheMasterClassForAWordPressTheme()
    {
        $module = new WPThemeModule();
        $module->setId('wptheme');
        $this->app->setModule('wptheme', $module);
        $module->init(null);
        $page = new StubThemedPage(new TTheme($this->themePath(), 'http://example.com/theme'));

        $module->installWPTheme($page, null);

        $this->assertSame(WPThemeMasterClassLayout::class, $page->getMasterClass());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInstallWPThemeLeavesOtherThemesAlone()
    {
        $module = new WPThemeModule();
        $module->setId('wptheme');
        $this->app->setModule('wptheme', $module);
        $module->init(null);
        $page = new StubThemedPage(new TTheme($this->plainThemePath(), 'http://example.com/theme'));

        $module->installWPTheme($page, null);

        $this->assertNull($page->getMasterClass());
    }

    public function testInstallWPThemeIgnoresAPageWithoutATheme()
    {
        $module = new WPThemeModule();
        $page = new StubThemedPage(null);

        $module->installWPTheme($page, null);

        $this->assertNull($page->getMasterClass());
    }
}

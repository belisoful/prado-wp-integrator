<?php

namespace PradoWpIntegrator\Test\Theme;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Prado\Util\TCallChain;
use Prado\Web\Services\TPageService;
use Prado\Web\UI\TForm;
use Prado\Web\UI\TPage;
use Prado\Web\UI\TTheme;
use Prado\Web\UI\WebControls\THead;
use PradoWpIntegrator\TestTools\OpenTheme;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\Theme\WPThemeBehavior;

/**
 * Test class for WPThemeBehavior.
 *
 * processParts() defines WP_THEME_PATH, WP_THEME_URI and WP_CONTENT_TAG and
 * includes the theme's PHP, so every test that reaches it runs in its own
 * process.
 */
class WPThemeBehaviorTest extends WPTestCase
{
    /** @var TTheme the theme the behavior is attached to; TBehavior keeps only a weak reference to its owner, so the test has to hold it */
    private $theme;

    /**
     * @param null|string $path the theme directory, defaults to the WordPress theme fixture
     * @return WPThemeBehavior a behavior attached to a theme
     */
    private function behaviorFor(?string $path = null): WPThemeBehavior
    {
        $this->theme = new TTheme($path ?? $this->themePath(), 'http://example.com/theme');
        $behavior = new WPThemeBehavior();
        $this->theme->attachBehavior('wpthemer', $behavior);

        return $behavior;
    }

    public function testGetSetContentPlaceHolderId()
    {
        $behavior = new WPThemeBehavior();
        $this->assertSame('', $behavior->getContentPlaceHolderId());

        $behavior->setContentPlaceHolderId('Main');

        $this->assertSame('Main', $behavior->getContentPlaceHolderId());
    }

    public function testGetIsAStub()
    {
        $this->assertNull((new WPThemeBehavior())->get('anything'));
    }

    public function testAWordPressThemeIsRecognised()
    {
        $this->assertTrue($this->behaviorFor()->isWordPressTheme());
    }

    public function testAThemeMissingWordPressFilesIsNotRecognised()
    {
        $this->assertFalse($this->behaviorFor($this->plainThemePath())->isWordPressTheme());
    }

    public function testTheWordPressThemeCheckIsCached()
    {
        $behavior = $this->behaviorFor();
        $this->assertTrue($behavior->isWordPressTheme());

        // the second call answers from the cached flag, without touching the disk
        $this->assertTrue($behavior->isWordPressTheme());
    }

    public function testAttachingWithoutAServiceDoesNothing()
    {
        // no service is running, so attach() must simply return
        $behavior = $this->behaviorFor();

        $this->assertTrue($behavior->getEnabled());
    }

    public function testAttachingToANonWordPressThemeDoesNothing()
    {
        $this->app->setService($this->pageServiceWithPage(new TPage()));

        $behavior = $this->behaviorFor($this->plainThemePath());

        $this->assertFalse($behavior->isWordPressTheme());
    }

    /**
     * Attaching to a WordPress theme during a page request subscribes the
     * behavior to the requested page, which is what rebuilds the page around
     * the theme.
     */
    public function testAttachingDuringAPageRequestHooksTheRequestedPage()
    {
        $page = new TPage();
        $this->app->setService($this->pageServiceWithPage($page));

        $this->behaviorFor();

        $this->assertNotEmpty($page->getEventHandlers('onInitComplete'));
    }

    /**
     * @param TPage $page the page the service should report as requested
     * @return TPageService a page service serving that page
     */
    private function pageServiceWithPage(TPage $page): TPageService
    {
        $service = new TPageService();
        $service->setId('page');
        $property = new \ReflectionProperty(TPageService::class, '_page');
        $property->setAccessible(true);
        $property->setValue($service, $page);

        return $service;
    }

    public function testStyleSheetsAreFilteredToStyleAndRtl()
    {
        $this->theme = new OpenTheme($this->themePath(), 'http://example.com/theme');
        $behavior = new WPThemeBehavior();
        $this->theme->attachBehavior('wpthemer', $behavior);
        $this->theme->setStyleSheetFiles([
            'http://example.com/theme/style.css',
            'http://example.com/theme/print.css',
            'http://example.com/theme/rtl.css',
            'http://example.com/theme/blocks.rtl.css',
            'http://example.com/theme/editor.rtl.min.css',
            'http://example.com/theme/editor.css',
        ]);

        $behavior->dyThemeProcess(new TCallChain('dyThemeProcess'));

        // style.css, anything ending in rtl.css, and any .rtl. variant survive
        $this->assertSame([
            'http://example.com/theme/style.css',
            'http://example.com/theme/rtl.css',
            'http://example.com/theme/blocks.rtl.css',
            'http://example.com/theme/editor.rtl.min.css',
        ], $this->theme->getStyleSheetFiles());
    }

    /**
     * TTheme::setStyleSheetFiles() is protected in pradosoft/prado 4.3.2, so
     * the filter a behavior computes cannot be written back to a plain theme.
     * This pins that limitation; WPThemeBehaviorTest uses OpenTheme to test the
     * filter itself.
     */
    public function testTheFilterCannotBeAppliedToAPlainTheme()
    {
        $behavior = $this->behaviorFor();
        $before = $behavior->getOwner()->getStyleSheetFiles();

        $behavior->dyThemeProcess(new TCallChain('dyThemeProcess'));

        $this->assertSame($before, $behavior->getOwner()->getStyleSheetFiles());
    }

    public function testPrintStylesheetsGetThePrintMediaType()
    {
        $behavior = $this->behaviorFor();

        $media = $behavior->dyCssMediaType('screen', 'http://example.com/theme/print.css', new TCallChain('dyCssMediaType'));

        $this->assertSame('print', $media);
    }

    public function testOtherStylesheetsKeepTheirMediaType()
    {
        $behavior = $this->behaviorFor();

        $media = $behavior->dyCssMediaType('screen', 'http://example.com/theme/style.css', new TCallChain('dyCssMediaType'));

        $this->assertSame('screen', $media);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProcessPartsSplitsTheRenderedTheme()
    {
        $behavior = $this->behaviorFor();

        [$preHead, $head, $preForm, $postForm, $preEndForm, $postEndForm] = $behavior->processParts();

        // the theme's functions.php is included before its index.php
        $this->assertTrue($GLOBALS['prado_test_theme_functions_loaded']);
        $this->assertSame(WP_THEME_PATH, $this->themePath());
        $this->assertSame('<wpcontent/>', WP_CONTENT_TAG);

        $this->assertStringContainsString('<html>', $preHead);
        $this->assertStringContainsString('<title>', $head);
        $this->assertStringNotContainsString('<head', $head);
        $this->assertStringContainsString('<body class="test-theme">', $preForm);
        $this->assertStringContainsString('PRE-FORM', $postForm);
        $this->assertStringContainsString('HEADER', $postForm);
        $this->assertStringContainsString('SIDEBAR', $preEndForm);
        $this->assertStringContainsString('FOOTER', $preEndForm);
        $this->assertStringContainsString('</html>', $postEndForm);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInstallWPThemeRebuildsThePageAroundTheTheme()
    {
        $behavior = $this->behaviorFor();
        $page = new TPage();
        $page->getControls()->add('EXISTING-CONTROL');

        $behavior->installWPTheme($page, null);

        $controls = $page->getControls();
        // pre-head text, the head, pre-form text, the form, then the tail
        $this->assertStringContainsString('<html>', $controls->itemAt(0));
        $this->assertInstanceOf(THead::class, $controls->itemAt(1));
        $this->assertStringContainsString('<body', $controls->itemAt(2));
        $form = $controls->itemAt(3);
        $this->assertInstanceOf(TForm::class, $form);
        $this->assertStringContainsString('</html>', $controls->itemAt(count($controls) - 1));

        // the page's own controls moved inside the form, between the theme's
        // pre-content and post-content markup
        $formControls = $form->getControls();
        $this->assertStringContainsString('PRE-FORM', $formControls->itemAt(0));
        $this->assertSame('EXISTING-CONTROL', $formControls->itemAt(1));
        $this->assertStringContainsString('SIDEBAR', $formControls->itemAt(count($formControls) - 1));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testInstallWPThemeReusesAnExistingHeadAndForm()
    {
        $behavior = $this->behaviorFor();
        $page = new TPage();
        $head = new THead();
        $form = new TForm();
        $form->getControls()->add('IN-FORM');
        $page->getControls()->add($head);
        $page->getControls()->add($form);
        $page->setHead($head);
        $page->setForm($form);

        $behavior->installWPTheme($page, null);

        $this->assertSame($head, $page->getControls()->itemAt(1));
        $this->assertSame($form, $page->getControls()->itemAt(3));
        $formControls = $form->getControls();
        $this->assertStringContainsString('PRE-FORM', $formControls->itemAt(0));
        $this->assertSame('IN-FORM', $formControls->itemAt(1));
    }
}

<?php

namespace PradoWpIntegrator\Test\Portlets;

use Prado\Web\UI\TTemplateControl;
use PradoWpIntegrator\Portlets\WPPostContent;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\WPTemplate;

/**
 * Test class for WPPostContent.
 */
class WPPostContentTest extends WPTestCase
{
    public function testConstructor()
    {
        $portlet = new WPPostContent();

        $this->assertInstanceOf(WPPostContent::class, $portlet);
        $this->assertInstanceOf(TTemplateControl::class, $portlet);
    }

    public function testGetSetPostId()
    {
        $portlet = new WPPostContent();
        $portlet->setPostId('123');

        $this->assertSame('123', $portlet->getPostId());
    }

    public function testPostIdDefaultsToEmpty()
    {
        $this->assertSame('', (new WPPostContent())->getPostId());
    }

    public function testOnLoadIsAStub()
    {
        $this->assertNull((new WPPostContent())->onLoad(null));
    }

    /**
     * TControl::getPluginModule() matches the control's file against the plugin
     * path of every plugin module on the application, so a portlet shipped with
     * this package finds the module on its own. Both portlets depend on it, so
     * this pins the framework behaviour they rely on.
     */
    public function testTheControlResolvesThePluginModuleFromTheApplication()
    {
        $module = $this->createModule();

        $this->assertSame($module, (new WPPostContent())->getPluginModule());
    }

    public function testTheControlHasNoPluginModuleWithoutOneOnTheApplication()
    {
        $this->assertNull((new WPPostContent())->getPluginModule());
    }

    public function testTheControlUsesAnAttachedPluginModule()
    {
        $module = $this->createModule();
        $portlet = new WPPostContent();

        $this->attachPluginModule($portlet, $module);

        $this->assertSame($module, $portlet->getPluginModule());
    }

    public function testLoadTemplateBuildsAWordPressTemplateForAPublishedPost()
    {
        $this->createModule();
        $portlet = new WPPostContent();
        $this->attachPluginModule($portlet);
        $portlet->setPostId(1);

        $template = $this->loadTemplate($portlet);

        $this->assertInstanceOf(WPTemplate::class, $template);
    }

    /**
     * @return array<string, array{0: array<string, mixed>}> posts that must not render
     */
    public static function unrenderablePostProvider(): array
    {
        return [
            'draft' => [['post_status' => 'draft', 'post_type' => 'post']],
            'password protected' => [['post_status' => 'publish', 'post_password' => 'secret', 'post_type' => 'post']],
            'attachment' => [['post_status' => 'publish', 'post_type' => 'attachment']],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unrenderablePostProvider')]
    public function testLoadTemplateIgnoresPostsThatMustNotBeShown(array $fields)
    {
        $this->createModule();
        $postId = $this->addPost($fields + ['post_content' => '<p>hidden</p>', 'post_title' => 'Hidden']);
        $portlet = new WPPostContent();
        $this->attachPluginModule($portlet);
        $portlet->setPostId($postId);

        $this->assertNull($this->loadTemplate($portlet));
    }

    public function testLoadTemplateAcceptsPages()
    {
        $this->createModule();
        $postId = $this->addPost(['post_type' => 'page', 'post_content' => '<p>page</p>']);
        $portlet = new WPPostContent();
        $this->attachPluginModule($portlet);
        $portlet->setPostId($postId);

        $this->assertInstanceOf(WPTemplate::class, $this->loadTemplate($portlet));
    }

    /**
     * @param WPPostContent $portlet the control under test
     * @return mixed whatever loadTemplate() returned
     */
    private function loadTemplate(WPPostContent $portlet)
    {
        $method = new \ReflectionMethod($portlet, 'loadTemplate');
        $method->setAccessible(true);
        return $method->invoke($portlet);
    }
}

<?php

namespace PradoWpIntegrator\Test\Portlets;

use Prado\IO\TTextWriter;
use Prado\Web\UI\WebControls\TLabel;
use PradoWpIntegrator\Portlets\WPPostContentTitle;
use PradoWpIntegrator\TestTools\WPTestCase;

/**
 * Test class for WPPostContentTitle.
 */
class WPPostContentTitleTest extends WPTestCase
{
    public function testConstructor()
    {
        $portlet = new WPPostContentTitle();

        $this->assertInstanceOf(WPPostContentTitle::class, $portlet);
        $this->assertInstanceOf(TLabel::class, $portlet);
    }

    public function testGetSetPostId()
    {
        $portlet = new WPPostContentTitle();
        $portlet->setPostId('123');

        $this->assertSame('123', $portlet->getPostId());
    }

    public function testPostIdDefaultsToEmpty()
    {
        $this->assertSame('', (new WPPostContentTitle())->getPostId());
    }

    public function testRenderContentsWritesTheTitleOfAPublishedPost()
    {
        $this->createModule();
        $portlet = new WPPostContentTitle();
        $this->attachPluginModule($portlet);
        $portlet->setPostId(1);
        $writer = new TTextWriter();

        $portlet->renderContents($writer);

        $this->assertSame('Hello', $writer->flush());
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
    public function testRenderContentsWritesNothingForPostsThatMustNotBeShown(array $fields)
    {
        $this->createModule();
        $postId = $this->addPost($fields + ['post_title' => 'Hidden']);
        $portlet = new WPPostContentTitle();
        $this->attachPluginModule($portlet);
        $portlet->setPostId($postId);
        $writer = new TTextWriter();

        $portlet->renderContents($writer);

        $this->assertSame('', $writer->flush());
    }
}

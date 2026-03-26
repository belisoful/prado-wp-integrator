<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\Portlets\WPPostContent;
use Prado\Web\UI\TTemplateControl;

/**
 * Test class for WPPostContent
 */
class WPPostContentTest extends TestCase
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
        $postId = '123';
        $portlet->setPostId($postId);
        $this->assertEquals($postId, $portlet->getPostId());
    }
}
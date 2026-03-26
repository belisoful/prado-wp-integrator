<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\Portlets\WPPostContentTitle;
use PradoWpIntegrator\WPIntegratorModule;
use Prado\Web\UI\WebControls\TLabel;

/**
 * Test class for WPPostContentTitle
 */
class WPPostContentTitleTest extends TestCase
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
        $postId = '123';
        $portlet->setPostId($postId);
        $this->assertEquals($postId, $portlet->getPostId());
    }
}
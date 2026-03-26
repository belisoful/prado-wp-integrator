<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPTemplate;
use Prado\Web\UI\TTemplate;

/**
 * Test class for WPTemplate
 */
class WPTemplateTest extends TestCase
{
    public function testConstructor()
    {
        $template = new WPTemplate('test content', '/tmp');
        $this->assertInstanceOf(WPTemplate::class, $template);
        $this->assertInstanceOf(TTemplate::class, $template);
    }
}
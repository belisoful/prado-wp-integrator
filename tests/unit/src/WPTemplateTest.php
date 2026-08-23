<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use Prado\Web\UI\TTemplate;
use PradoWpIntegrator\WPTemplate;

/**
 * Test class for WPTemplate.
 */
class WPTemplateTest extends TestCase
{
    public function testConstructor()
    {
        $template = new WPTemplate('test content', '/tmp');

        $this->assertInstanceOf(WPTemplate::class, $template);
        $this->assertInstanceOf(TTemplate::class, $template);
    }

    public function testTheTemplateKeepsItsWordPressContent()
    {
        $template = new WPTemplate('<p>post body</p>', null);

        // TTemplate stores each item as [parent index, content]
        $items = $template->getItems();
        $this->assertSame('<p>post body</p>', $items[0][1]);
    }
}

<?php

namespace PradoWpIntegrator\Test\Theme;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\TestTools\ChildProcess;

/**
 * Test class for the shims WordPress itself normally supplies.
 *
 * The PHPUnit bootstrap loads wp-includes/plugin.php, so add_filter(),
 * apply_filters(), add_action() and do_action() are the real WordPress
 * functions in the test process - which is what the guards are for. Their shim
 * bodies therefore only run in a process without WordPress, which is what these
 * tests create. Coverage from those children is merged by
 * tests/test_tools/coverage.php.
 */
class WPFunctionsIsolatedTest extends TestCase
{
    /**
     * @param string $code the php to run after including the shims
     * @return array{status: int, output: string} the child result
     */
    private function runWithShims(string $code): array
    {
        $shim = realpath(__DIR__ . '/../../../src/Theme/WPFunctions.php');
        return ChildProcess::run('include ' . var_export($shim, true) . ";\n" . $code);
    }

    public function testPluginApiShimsAreUsedWithoutWordPress()
    {
        $result = $this->runWithShims(
            'echo var_export(add_filter("f", "cb"), true), "|",'
            . ' var_export(add_action("a", "cb"), true), "|",'
            . ' var_export(do_action("a"), true), "|",'
            . ' apply_filters("f", "data"), "|",'
            . ' apply_filters("f", "data", "extra1", "extra2");'
        );

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertSame('NULL|NULL|NULL|data|data', $result['output']);
    }

    public function testShimsLoadWithoutWordPressOrPrado()
    {
        $result = $this->runWithShims('echo get_the_title(), "|", absint(-5), "|", (int) is_home();');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertSame('post title|5|1', $result['output']);
    }
}

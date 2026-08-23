<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\TestTools\ChildProcess;

/**
 * Test class for src/composer.php, the WordPress-directory resolver.
 *
 * Resolution happens at include time and defines constants, so each case runs
 * in its own child process with its own environment.
 */
class ComposerBootstrapTest extends TestCase
{
    /** @var string[] temporary directories to remove when the test ends */
    private $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            $this->removeDirectory($directory);
        }
        $this->temporaryDirectories = [];
        parent::tearDown();
    }

    /**
     * @param array $env environment variables for the child
     * @param string $code php to run after including the bootstrap
     * @return array{status: int, output: string} the child result
     */
    private function includeBootstrap(array $env = [], string $code = ''): array
    {
        $bootstrap = realpath(__DIR__ . '/../../src/composer.php');
        return ChildProcess::run('require ' . var_export($bootstrap, true) . ";\n" . $code, $env);
    }

    public function testResolvesTheRepositoryWordPressByDefault()
    {
        $expected = realpath(__DIR__ . '/../../wordpress');
        $result = $this->includeBootstrap([], 'echo PRADO_WP_DIR;');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringEndsWith($expected, $result['output']);
    }

    public function testWpDirEnvironmentVariableWins()
    {
        $expected = realpath(__DIR__ . '/../../wordpress');
        $result = $this->includeBootstrap(['WP_DIR' => $expected], 'echo PRADO_WP_DIR;');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringEndsWith($expected, $result['output']);
    }

    public function testUnusableWpDirFallsBackToTheRepositoryWordPress()
    {
        $expected = realpath(__DIR__ . '/../../wordpress');
        $result = $this->includeBootstrap(['WP_DIR' => '/nonexistent-wordpress'], 'echo PRADO_WP_DIR;');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringEndsWith($expected, $result['output']);
    }

    public function testDefinesTheCliConstantsAndLoadsWordPress()
    {
        $result = $this->includeBootstrap([], 'echo WP_SITEURL, "|", (int) function_exists("add_filter"), "|", (int) class_exists("WP_Session_Tokens", false);');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertSame('cli|1|1', $result['output']);
    }

    public function testContentDirectoryIsDefinedWhenWordPressHasOne()
    {
        $wordpress = $this->makeFakeWordPress(true);
        $result = $this->includeBootstrap(['WP_DIR' => $wordpress], 'echo WP_CONTENT_DIR;');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringEndsWith('wp-content', $result['output']);
    }

    public function testMissingWordPressFilesWarnWithoutFailing()
    {
        $wordpress = $this->makeFakeWordPress(false);
        $result = $this->includeBootstrap(['WP_DIR' => $wordpress], 'echo "|reached";');

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringContainsString('WordPress file not found', $result['output']);
        $this->assertStringEndsWith('|reached', $result['output']);
    }

    public function testWarnsWhenNoWordPressCanBeFound()
    {
        // a directory layout where neither the repository nor the project root
        // has a wordpress/ directory: copy the resolver somewhere isolated
        $sandbox = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('prado-wp-nowp-', true);
        mkdir($sandbox . '/src', 0o777, true);
        $this->temporaryDirectories[] = $sandbox;
        copy(realpath(__DIR__ . '/../../src/composer.php'), $sandbox . '/src/composer.php');

        $result = ChildProcess::run(
            'require ' . var_export($sandbox . '/src/composer.php', true) . '; echo "|after";',
            ['WP_DIR' => '/nonexistent-wordpress']
        );

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringContainsString('WordPress directory not found', $result['output']);
        // the resolver returns from the include, so the caller keeps running
        $this->assertStringEndsWith('|after', $result['output']);
    }

    public function testAPredefinedWordPressDirectoryIsHonoured()
    {
        $wordpress = $this->makeFakeWordPress(true);
        $result = ChildProcess::run(
            'define("PRADO_WP_DIR", ' . var_export($wordpress, true) . ');'
            . ' require ' . var_export(realpath(__DIR__ . '/../../src/composer.php'), true) . ';'
            . ' echo PRADO_WP_DIR, "|", WP_CONTENT_DIR;'
        );

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertSame($wordpress . '|' . $wordpress . '/wp-content', $result['output']);
    }

    public function testAnEmptyPredefinedDirectoryStopsWordPressFromLoading()
    {
        $result = ChildProcess::run(
            'define("PRADO_WP_DIR", "");'
            . ' require ' . var_export(realpath(__DIR__ . '/../../src/composer.php'), true) . ';'
            . ' echo "|", (int) function_exists("add_filter");'
        );

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertStringContainsString('WordPress directory not found', $result['output']);
        // the include returned early, so WordPress was never loaded
        $this->assertStringEndsWith('|0', $result['output']);
    }

    public function testIncludingTwiceIsSafe()
    {
        $bootstrap = realpath(__DIR__ . '/../../src/composer.php');
        $result = ChildProcess::run(
            'require ' . var_export($bootstrap, true) . ';'
            . ' require ' . var_export($bootstrap, true) . '; echo "OK";'
        );

        $this->assertSame(0, $result['status'], $result['output']);
        $this->assertSame('OK', $result['output']);
    }

    /**
     * @param bool $withContent whether to create a wp-content directory
     * @return string the fake WordPress path
     */
    private function makeFakeWordPress(bool $withContent): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('prado-wp-fake-', true);
        mkdir($path, 0o777, true);
        $this->temporaryDirectories[] = $path;
        file_put_contents($path . '/wp-load.php', "<?php\n");
        if ($withContent) {
            mkdir($path . '/wp-includes', 0o777, true);
            file_put_contents($path . '/wp-includes/class-wp-session-tokens.php', "<?php\n");
            file_put_contents($path . '/wp-includes/plugin.php', "<?php\n");
            mkdir($path . '/wp-content', 0o777, true);
        }
        return $path;
    }

    /**
     * @param string $path the directory to remove recursively
     */
    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $path . DIRECTORY_SEPARATOR . $entry;
            is_dir($full) ? $this->removeDirectory($full) : @unlink($full);
        }
        @rmdir($path);
    }
}

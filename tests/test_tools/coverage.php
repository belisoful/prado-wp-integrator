<?php

/**
 * Coverage driver.
 *
 * Runs the unit suite with coverage, then merges the coverage recorded by test
 * child processes (see ChildProcess/child-bootstrap.php) into the PHPUnit
 * report before rendering it. Without the merge, code that can only run in a
 * process this repository's PHPUnit bootstrap has not prepared - the theme
 * shims WordPress would otherwise supply, the include-time resolution in
 * src/composer.php - reads as uncovered.
 *
 * Usage: composer coverage [-- <extra phpunit arguments>]
 *
 * @author Brad Anderson <belisoful@icloud.com>
 */

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Thresholds;

require_once __DIR__ . '/../../vendor/autoload.php';

$root = dirname(__DIR__, 2);
$buildDir = $root . '/build/coverage';
$childDir = $buildDir . '/children';
$phpunitCov = $buildDir . '/phpunit.cov';

removeDirectory($buildDir);
mkdir($childDir, 0o777, true);

$arguments = array_slice($argv, 1);
$command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/vendor/bin/phpunit')
    . ' --testsuite unit --coverage-php ' . escapeshellarg($phpunitCov);
foreach ($arguments as $argument) {
    $command .= ' ' . escapeshellarg($argument);
}

putenv('XDEBUG_MODE=coverage');
putenv('PRADO_WP_COVERAGE_DIR=' . $childDir);
passthru($command, $status);

if (!is_file($phpunitCov)) {
    fwrite(STDERR, "coverage.php: PHPUnit produced no coverage report\n");
    exit($status === 0 ? 1 : $status);
}

/** @var CodeCoverage $coverage */
$coverage = include $phpunitCov;

$children = glob($childDir . '/*.cov') ?: [];
foreach ($children as $child) {
    $coverage->merge(include $child);
}

(new Clover())->process($coverage, $buildDir . '/clover.xml');
(new HtmlReport())->process($coverage, $buildDir . '/html');

echo "\nMerged coverage (PHPUnit + " . count($children) . " child process"
    . (count($children) === 1 ? '' : 'es') . "):\n";
echo (new TextReport(Thresholds::default(), false, false))->process($coverage, false);
echo "HTML report: build/coverage/html/index.html\n";

exit($status);

/**
 * @param string $path the directory to remove recursively
 */
function removeDirectory(string $path): void
{
    if (!is_dir($path)) {
        return;
    }
    foreach (scandir($path) as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $full = $path . DIRECTORY_SEPARATOR . $entry;
        is_dir($full) ? removeDirectory($full) : @unlink($full);
    }
    @rmdir($path);
}

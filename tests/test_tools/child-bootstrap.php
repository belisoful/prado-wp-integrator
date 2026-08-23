<?php

/**
 * Bootstrap for test child processes.
 *
 * Some behaviour can only be exercised in a process that this repository's
 * PHPUnit bootstrap has NOT prepared: the shims that WordPress itself would
 * otherwise declare, and the WordPress-directory resolution in
 * src/composer.php, which runs at include time. Those tests run a script
 * through this bootstrap with `php <bootstrap> <script>`.
 *
 * When PRADO_WP_COVERAGE_DIR is set, the child records its own line coverage
 * and writes a .cov file into that directory; tests/test_tools/coverage.php
 * merges those files into the PHPUnit report. Without it the child simply runs.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 */

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;

require_once __DIR__ . '/../../vendor/autoload.php';

$script = $argv[1] ?? null;
if ($script === null || !is_file($script)) {
	fwrite(STDERR, "child-bootstrap.php: no script to run\n");
	exit(2);
}

$coverageDir = getenv('PRADO_WP_COVERAGE_DIR');
if (is_string($coverageDir) && $coverageDir !== '' && is_dir($coverageDir)) {
	$filter = new Filter();
	$filter->includeDirectory(realpath(__DIR__ . '/../../src'));
	$coverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
	$coverage->start('child:' . basename($script));

	register_shutdown_function(static function () use ($coverage, $coverageDir, $script) {
		$coverage->stop();
		$name = basename($script, '.php') . '-' . getmypid() . '-' . uniqid() . '.cov';
		(new PhpReport())->process($coverage, $coverageDir . DIRECTORY_SEPARATOR . $name);
	});
}

require $script;

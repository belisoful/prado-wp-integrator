<?php
/**
 * PHPUnit bootstrap.
 *
 * WordPress is located by src/composer.php (WP_DIR env var, then ./wordpress
 * installed by roots/wordpress). roots/wordpress ships no wp-content, so unless
 * the resolved install has one, WP_CONTENT_DIR points at the stub content
 * directory in tests/fixtures (which contains a minimal theme).
 */

require_once(__DIR__ . '/../../vendor/autoload.php');

// PRADO's application singleton refuses to be replaced unless this is defined,
// which would make one TApplication per test impossible.
if (! defined('PRADO_TEST_RUN')) {
	define('PRADO_TEST_RUN', true);
}

if (! defined('WP_CONTENT_DIR')) {
	$wpDir = getenv('WP_DIR');
	if (! is_string($wpDir) || $wpDir === '' || ! is_dir($wpDir . '/wp-content')) {
		define('WP_CONTENT_DIR', realpath(__DIR__ . '/../fixtures/wp-content'));
	}
	unset($wpDir);
}

require_once(__DIR__ . '/../../src/composer.php');

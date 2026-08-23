<?php

/**
 * Bootstrap for the PRADO WordPress Integrator.
 *
 * This file is idempotent; it may be required more than once.
 *
 * Locating WordPress:
 *  WordPress core is NOT a runtime dependency of this package. The WordPress
 *  directory is resolved, in order, from:
 *   1. The `WP_DIR` environment variable (an existing WordPress install), e.g.
 *        WP_DIR=/var/www/wordpress composer unittest
 *   2. `wordpress/` in this repository (installed by `roots/wordpress` in
 *      require-dev via `extra.wordpress-install-dir`).
 *   3. `wordpress/` in the root of the consuming project (when this package is
 *      installed under vendor/ and the project uses roots/wordpress or
 *      johnpbloch/wordpress with the default install dir).
 *  The result is exposed as the `PRADO_WP_DIR` constant ('' when not found).
 *  Defining `PRADO_WP_DIR` before including this file overrides resolution
 *  entirely; define it as '' to keep this file from loading WordPress at all.
 *  At runtime the module's `WPDirectory` property is the authoritative link
 *  to WordPress; this resolver only supplies a default and drives CLI/test
 *  bootstrapping.
 *
 * Content directory:
 *  `roots/wordpress` ships core only (no wp-content). If `WP_CONTENT_DIR` is not
 *  defined and PRADO_WP_DIR has no wp-content/, define `WP_CONTENT_DIR` before
 *  including this file to point at your themes/plugins (tests do this).
 *
 * @author Brad Anderson <belisoful@icloud.com>
 */
if (file_exists($autoloader = realpath(__DIR__ . '/../vendor/autoload.php'))) {
	// if we are running inside a prado-wp-integrator repo checkout, get out of src/
	include_once($autoloader);
}

if (! defined('PRADO_WP_DIR')) {
	$_pradoWpCandidates = [];
	$_pradoWpEnv = getenv('WP_DIR');
	if (is_string($_pradoWpEnv) && $_pradoWpEnv !== '') {
		$_pradoWpCandidates[] = $_pradoWpEnv;
	}
	$_pradoWpCandidates[] = __DIR__ . '/../wordpress';
	$_pradoWpCandidates[] = __DIR__ . '/../../../../wordpress';

	$_pradoWpDir = '';
	foreach ($_pradoWpCandidates as $_pradoWpCandidate) {
		$_pradoWpReal = realpath($_pradoWpCandidate);
		if ($_pradoWpReal !== false && is_file($_pradoWpReal . '/wp-load.php')) {
			$_pradoWpDir = $_pradoWpReal;
			break;
		}
	}
	define('PRADO_WP_DIR', $_pradoWpDir);
	unset($_pradoWpCandidates, $_pradoWpEnv, $_pradoWpCandidate, $_pradoWpReal, $_pradoWpDir);
}

if (php_sapi_name() === 'cli') {
	if (! defined('WP_SITEURL')) {
		define('WP_SITEURL', 'cli');
	}
	if (PRADO_WP_DIR === '') {
		echo("WARNING: WordPress directory not found, expect errors. Set WP_DIR or run `composer install` (roots/wordpress).\n\n");
		return;
	}
	if (! defined('WP_CONTENT_DIR') && is_dir(PRADO_WP_DIR . '/wp-content')) {
		define('WP_CONTENT_DIR', PRADO_WP_DIR . '/wp-content');
	}
	foreach (['/wp-includes/class-wp-session-tokens.php', '/wp-includes/plugin.php'] as $_pradoWpFile) {
		$_pradoWpFile = PRADO_WP_DIR . $_pradoWpFile;
		if (file_exists($_pradoWpFile)) {
			include_once($_pradoWpFile);
		} else {
			echo("WARNING: WordPress file not found, expect errors.\n\n" . $_pradoWpFile . "\n");
		}
	}
	unset($_pradoWpFile);
}

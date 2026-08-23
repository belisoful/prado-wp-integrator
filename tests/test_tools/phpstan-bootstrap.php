<?php
/**
 * PHPStan bootstrap.
 *
 * Static analysis uses php-stubs/wordpress-stubs; a WordPress checkout is not
 * required. ABSPATH is defined for code paths that reference it, using the
 * same resolution as src/composer.php (WP_DIR env var, then ./wordpress).
 *
 * The WordPress cookie constants are defined at runtime by
 * WPIntegratorModule::initializeWPConstants(), so they are declared here for
 * the code that reads them.
 */

require_once(__DIR__ . '/../../src/composer.php');

if (! defined('ABSPATH')) {
    define('ABSPATH', (PRADO_WP_DIR !== '' ? PRADO_WP_DIR : realpath(__DIR__ . '/../..') . '/wordpress') . '/');
}

if (! defined('COOKIEHASH')) {
    define('COOKIEHASH', '');
}
foreach ([
    'USER_COOKIE' => 'wordpressuser_',
    'PASS_COOKIE' => 'wordpresspass_',
    'AUTH_COOKIE' => 'wordpress_',
    'SECURE_AUTH_COOKIE' => 'wordpress_sec_',
    'LOGGED_IN_COOKIE' => 'wordpress_logged_in_',
    'RECOVERY_MODE_COOKIE' => 'wordpress_rec_',
] as $constant => $prefix) {
    if (! defined($constant)) {
        define($constant, $prefix . COOKIEHASH);
    }
}

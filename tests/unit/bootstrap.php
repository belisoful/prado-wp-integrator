<?php

// Bootstrap file for tests; defers to the shared PHPUnit bootstrap.
require_once __DIR__ . '/../test_tools/phpunit_bootstrap.php';

if (! defined('WP_LOAD_PATH') && PRADO_WP_DIR !== '') {
    define('WP_LOAD_PATH', PRADO_WP_DIR . '/wp-load.php');
}

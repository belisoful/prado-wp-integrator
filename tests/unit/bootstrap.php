<?php

// Bootstrap file for tests
require_once __DIR__ . '/../../../vendor/autoload.php';

// Set up the test environment
if (!defined('WP_LOAD_PATH')) {
    define('WP_LOAD_PATH', __DIR__ . '/../../../wordpress-6.9.4/wp-load.php');
}
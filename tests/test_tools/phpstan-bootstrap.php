<?php
/**
 * A few common settings for all unit tests.
 *
 * Also remember do define the @package attribute for your test class to make it appear under
 * the right package in unit test and code coverage reports.
 */
 
//$_wordpress_directory = '/../wordpress/wp-load.php';
//$_wordpress_directory = '/../../../wordpress-6.9.4/wp-load.php';
if ( ! defined( 'COOKIEHASH' ) ) {
    define('COOKIEHASH', '923098902380942840372');
}
if ( ! defined( 'LOGGED_IN_COOKIE' ) ) {
    define( 'LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH );
}

$basePath = realpath(__DIR__ . '/../../../wordpress-6.9.4/');

$directories = [
    '/wp-includes/class-wp-session-tokens.php',
    '/wp-includes/plugin.php',
    ];
 
if (php_sapi_name() === 'cli') {
    if ( ! defined( 'WP_SITEURL' ) ) {
        define('WP_SITEURL', 'cli');
    }
    if (!$basePath) {
        echo("WARNING: WordPress directory not found, expect errors.\n\n" . __DIR__ . $_wordpress_directory . "\n\n");
        return;
    }
    foreach ($directories as $file) {
        $file = $basePath . $file;
        if (file_exists($file)) {
            include($file);
        } else {
            echo("WARNING: WordPress directory not found, expect errors.\n\n" . $file . "\n");
        }
    }
    /*if ($_wordpress_directory && file_exists($autoloader = realpath(__DIR__ . $_wordpress_directory))) {
 
        //echo("\n\n" . WP_SITEURL . "\n\n");
        // include wordpress custom
        include($autoloader);
        echo("\nTHERE\n");
    } else {
        echo("WARNING: WordPress directory not found, expect errors.\n\n" . __DIR__ . $_wordpress_directory . "\n\n");
    }*/
}

// Project Includes
//   None.

<?php
/**
 * A few common settings for all unit tests.
 *
 * Also remember do define the @package attribute for your test class to make it appear under
 * the right package in unit test and code coverage reports.
 */
 
 $_wordpress_directory = '/../wordpress/wp-load.php';
 $_wordpress_directory = '/../../../wordpress-6.9.4/wp-load .php';
 
 if (php_sapi_name() === 'cli') {
     define('WP_SITEURL', 'cli');
     if ($_wordpress_directory && file_exists($autoloader = realpath(__DIR__ . $_wordpress_directory))) {
 
         //echo("\n\n" . WP_SITEURL . "\n\n");
         // include wordpress custom
         include($autoloader);
          echo("\nTHERE\n");
     } else {
         echo("WARNING: WordPress directory not found, expect errors.\n\n" . __DIR__ . $_wordpress_directory . "\n\n");
     }
 }

// Project Includes
//   None.

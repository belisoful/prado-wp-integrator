<?php

$_wordpress_directory = '/../wordpress/wp-load.php';
$_wordpress_directory = '/../../wordpress-6.9.4/wp-load.php';

if (file_exists($autoloader = realpath(__DIR__ . '/../vendor/autoload.php'))) {
	// if we are running inside a prado-wp-integrator repo checkout, get out of src/
	include($autoloader);
}
if (php_sapi_name() === 'cli') {
	define('WP_SITEURL', 'cli');
	if ($_wordpress_directory && file_exists($autoloader = realpath(__DIR__ . $_wordpress_directory))) {

		//echo("\n\n" . WP_SITEURL . "\n\n");
		// include wordpress custom
		include($autoloader);
	} else {
		echo("WARNING: WordPress directory not found, expect errors.\n\n" . __DIR__ . $_wordpress_directory . "\n\n");
	}
}

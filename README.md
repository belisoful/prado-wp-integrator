# PRADO WordPress Integrator

[![Build Status](https://travis-ci.org/belisoful/prado-wp-integrator.svg?branch=master)](https://travis-ci.org/belisoful/prado-wp-integrator)

This PRADO module integrates WordPress into PRADO applications, providing access to WordPress content and users within PRADO applications.

# Experimental Code
* This has not been tested with current versions of WordPress (since years ago).
* WordPress core is not a Composer dependency of this package. Point the module's `WPDirectory` property at an existing WordPress installation (the directory containing `wp-load.php`), or install core into your project with `composer require roots/wordpress` (core only, no bundled themes) and point `WPDirectory` at its install directory (default `wordpress/`).

## Features
* Access to WordPress posts and pages
* WordPress user authentication
* WordPress database integration
* Seamless integration with PRADO's authentication and user management

## Requirements
* PRADO 4.2.0 or higher
* PHP 7.4 or higher
* WordPress 4.0 or higher

## Install

The best way to install PRADO WordPress Integrator is [through composer](http://getcomposer.org).

Integrate the PRADO WordPress Integrator using composer:
```sh
composer require belisoful/prado-wp-integrator
```

The plugin will be installed in the "vendor" directory.

### Add PRADO WordPress Integrator to an existing application

Just create a composer.json file for your project:
```JSON
{
  "require": {
    "belisoful/prado-wp-integrator": "^0.0.1"
  }
}
```

The PRADO WordPress Integrator is a PRADO 7.4+ extension to plug in new functionality directly into a PRADO application.

### Development / testing against WordPress

The test suite and PHPStan bootstrap locate WordPress in this order:

1. the `WP_DIR` environment variable — an existing WordPress install, e.g. `WP_DIR=/var/www/wordpress composer unittest`;
2. `wordpress/` in the repository root, installed by `roots/wordpress` (a `require-dev` dependency) on `composer install`.

`roots/wordpress` ships no `wp-content/`; when the resolved install has none, tests use the stub content directory in `tests/fixtures/wp-content` (which contains a minimal theme) as `WP_CONTENT_DIR`.

## Documentation

The [Tutorial](https://github.com/belisoful/prado-wp-integrator)

## Usage
To use this module:
1. Add the module to your PRADO application configuration
2. Configure database connection parameters
3. Access WordPress content from your PRADO application

```xml
<module id="belisoful\prado-wp-integrator" 
         ConnectionID="db"
         WPUserManagerID="wpusermanager"
         WPAuthManagerID="wpauthmanager"
         WPDbParameterID="wpdbparameter"
         DatabasePrefix="wp_"/>
```

`WPDirectory` is the link to WordPress: set it to the directory containing
`wp-load.php`.

### Examples

The [examples](examples/) directory contains a sample page (`Pages/WPTest`)
that renders a WordPress post through the `WPPostContent` portlets. Copy it into
your application's page directory; it is not part of the autoloaded package
source and is excluded from distribution archives.

### WordPress themes without WordPress

`WPThemeModule` can render a WordPress theme without loading WordPress: the
theme's `functions.php` and `index.php` are executed against the shims in
`src/Theme/WPFunctions.php`. Every shim is guarded with `function_exists()` /
`class_exists()`, so a real WordPress installation - or a partially loaded one -
always takes precedence.

## Contributing

In the spirit of free software, **everyone** is encouraged to help improve this project.

Here are some ways *you* can contribute:

* by using prerelease versions
* by reporting bugs
* by writing specifications
* by writing code (*no patch is too small*: fix typos, add comments, clean up inconsistent whitespace)
* by refactoring code
* by resolving issues
* by reviewing patches
* by writing your own PRADO extension and posting it on [Packagist](https://packagist.org/)
* by supporting and contributing to your favorite PRADO extensions

Starting point:

* Fork the repo
* Clone your repo
* Make your changes
* Write tests for your changes to ensure that later changes to PRADO WordPress Integrator won't break your code.
* Submit your pull request

## Testing

PRADO WordPress Integrator uses phpunit (https://phpunit.de/) for unit testing.

In order to run tests, first clone the PRADO WordPress Integrator repository and have composer install the needed development libraries:
```
git clone https://github.com/belisoful/prado-wp-integrator.git
cd prado-wp-integrator
composer upgrade
```

The package requires `pradosoft/prado: ^4.3@dev`, which lets an application pick
its own framework build, so a plain `composer upgrade` here floats with whatever
that resolves to. The test suite pins framework behaviour, so CI installs one
build, named by `PRADO_VERSION` in `.github/workflows/prado-wp-integrator.yml`.
To run the tests against the same build CI does:
```
composer require --no-update pradosoft/prado:"4.4.x-dev#<the sha in PRADO_VERSION>"
composer update
```
Bump `PRADO_VERSION` to adopt a newer framework build, or run the workflow by
hand with a `prado_version` input (for example `4.4.x-dev`) to test against a
branch head before pinning it.

Now you are ready to run tests; a phpunit configuration file is provided, to run the tests just execute
```composer unittest``` to run unit tests.

Test results will be saved in in the `build/tests/` directory.

To run the suite with code coverage (requires Xdebug or PCOV):
```
composer coverage
```
This merges the coverage recorded by test child processes into the PHPUnit
report and writes `build/coverage/clover.xml` and `build/coverage/html/`.
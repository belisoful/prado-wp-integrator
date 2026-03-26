# PRADO WordPress Integrator

[![Build Status](https://travis-ci.org/belisoful/prado-wp-integrator.svg?branch=master)](https://travis-ci.org/belisoful/prado-wp-integrator)

This PRADO module integrates WordPress into PRADO applications, providing access to WordPress content and users within PRADO applications.

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

Not yet Implemented: PRADO WordPress Integrator uses phpunit (https://phpunit.de/) for unit testing.

In order to run tests, first clone the PRADO WordPress Integrator repository and have composer install the needed development libraries:
```
git clone https://github.com/belisoful/prado-wp-integrator.git
cd prado-wp-integrator
composer upgrade
```

Now you are ready to run tests; a phpunit configuration file is provided, to run the tests just execute
```composer unittest``` to run unit tests and

Test results will be saved in in the `build/tests/` directory.
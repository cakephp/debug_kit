# CakePHP DebugKit
[![Build Status](https://secure.travis-ci.org/cakephp/debug_kit.png?branch=master)](http://travis-ci.org/cakephp/debug_kit)
[![Coverage Status](https://img.shields.io/codecov/c/github/cakephp/debug_kit.svg?style=flat-square)](https://codecov.io/github/cakephp/debug_kit)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Total Downloads](https://img.shields.io/packagist/dt/cakephp/cakephp.svg?style=flat-square)](https://packagist.org/packages/cakephp/debug_kit)

DebugKit provides a debugging toolbar and enhanced debugging tools for CakePHP
applications. It lets you quickly see configuration data, log messages, SQL
queries, and timing data for your application.

:warning: DebugKit is only intended for use in single-user local development
environments. You should avoid using DebugKit in shared development
environments, staging environments, or any environment where you need to keep
configuration data and environment variables hidden. :warning:

## Requirements

* SQLite (pdo_sqlite) or another database driver that CakePHP can talk to. By
  default DebugKit will use SQLite, if you need to use a different database see the Database Configuration section in the documentation linked below.

For details and older versions see [version map](https://github.com/cakephp/debug_kit/wiki#version-map).

## Installation

* Install the plugin with [Composer](https://getcomposer.org/) from your CakePHP Project's ROOT directory (where the **composer.json** file is located)
```sh
php composer.phar require --dev cakephp/debug_kit:"^4.0"
```

* [Load the plugin](https://book.cakephp.org/4/en/plugins.html#loading-a-plugin)
```php
// src/Application.php
$this->addPlugin('DebugKit');
```
* Set `'debug' => true,` in `config/app.php`.

## Reporting Issues

If you have a problem with DebugKit please open an issue on [GitHub](https://github.com/cakephp/debug_kit/issues).

## Contributing

If you'd like to contribute to DebugKit, check out the
[roadmap](https://github.com/cakephp/debug_kit/wiki/roadmap) for any
planned features. You can [fork](https://help.github.com/articles/fork-a-repo)
the project, add features, and send [pull
requests](https://help.github.com/articles/using-pull-requests) or open
[issues](https://github.com/cakephp/debug_kit/issues).

## Documentation

Documentation for DebugKit can be found in the 
[CakePHP documentation](https://book.cakephp.org/debugkit/4/en/index.html).

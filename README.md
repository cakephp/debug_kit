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

The `master` branch has the following requirements:

* CakePHP 3.6.0 or greater.
* PHP 5.6.0 or greater.
* SQLite (pdo_sqlite) or another database driver that CakePHP can talk to. By
  default DebugKit will use SQLite, if you need to use a different database see
  the Database Configuration section below.

## DebugKit for CakePHP 2.x

If you want DebugKit for your 2.x application, you can use the latest `2.2.y` tag or the [2.2 branch](https://github.com/cakephp/debug_kit/tree/2.2).

## Installation

* Install the plugin with [Composer](https://getcomposer.org/) from your CakePHP Project's ROOT directory (where the **composer.json** file is located)
```sh
php composer.phar require --dev cakephp/debug_kit "~3.0"
```

* [Load the plugin](http://book.cakephp.org/3.0/en/plugins.html#loading-a-plugin)
```php
Plugin::load('DebugKit', ['bootstrap' => true, 'routes' => true]);
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

## Versions

DebugKit has several releases, each compatible with different releases of
CakePHP. Use the appropriate version by downloading a tag, or checking out the
correct branch.

* `1.0, 1.1, 1.2` are compatible with CakePHP 1.2.x. These releases of DebugKit
  will not work with CakePHP 1.3. You can also use the `1.2-branch` for the mos
  recent updates and bugfixes.
* `1.3.0` is compatible with CakePHP 1.3.x only. It will not work with CakePHP
  1.2. You can also use the `1.3` branch to get the most recent updates and
  bugfixes.
* `2.2.x` are compatible with CakePHP 2.2.0 and greater. It is a necessary
  upgrade for people using CakePHP 2.4 as the naming conventions around loggers
  changed in that release. 2.2.x is not actively being developed.
* `3.x` is compatible with CakePHP 3.x and is still under active development.

# Documentation

Documentation for DebugKit can be found in the 
[CakePHP documentation](https://book.cakephp.org/3.0/en/debug-kit.html).

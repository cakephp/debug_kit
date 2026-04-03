# DebugKit

DebugKit provides a debugging toolbar and enhanced debugging tools for CakePHP applications. It lets you quickly inspect configuration data, log messages, SQL queries, timers, mail previews, request history, and more while developing locally.

::: warning
DebugKit is only intended for use in single-user local development environments. Avoid using DebugKit in shared development, staging, or production-like environments where configuration and environment data must remain private.
:::

## Requirements

DebugKit stores panel data in a database. The default setup uses SQLite through `pdo_sqlite`. If SQLite is not available, define a dedicated `debug_kit` datasource instead.

## Installation

Install the plugin with Composer from your application's root directory:

```bash
php composer.phar require --dev cakephp/debug_kit:"^5.0"
```

Then load the plugin in debug mode:

```bash
bin/cake plugin load DebugKit --only-debug
```

## Troubleshooting

If the toolbar icon does not appear in the bottom-right corner of the page, check these common issues:

1. SQLite is not installed, and DebugKit cannot persist panel data.
2. Your hostname is not recognized as a safe development host. Add the TLD to `DebugKit.safeTld`.
3. You are using the Authorization plugin and need to set `DebugKit.ignoreAuthorization` to `true`.

## Documentation Map

The English documentation is split into focused sections:

* [Configuration](/configuration) covers the available settings and database storage.
* [Toolbar Usage](/toolbar) introduces the built-in panels.
* [History Panel](/history-panel) explains request history and replaying prior requests.
* [Mail Panel](/mail-panel) shows mail capture and preview support.
* [Custom Panels](/custom-panels) explains how to build your own panels.
* [API Requests](/api-requests) covers toolbar access for API-only applications.
* [Helper Functions](/helper-functions) documents the SQL helpers, timers, and query tracing.

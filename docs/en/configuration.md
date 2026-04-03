# Configuration

DebugKit supports several configuration keys that let you tailor the toolbar for local development.

* `DebugKit.panels` enables or disables individual panels:

```php
// Before loading DebugKit
Configure::write('DebugKit.panels', ['DebugKit.Packages' => false]);
```

* `DebugKit.includeSchemaReflection` enables logging for schema reflection queries. It is disabled by default.
* `DebugKit.safeTld` defines additional local TLDs that should be considered safe:

```php
Configure::write('DebugKit.safeTld', ['test', 'local', 'example']);
```

* `DebugKit.forceEnable` forces the toolbar to display. Whitelisting local TLDs is usually safer:

```php
Configure::write('DebugKit.forceEnable', true);

Configure::write('DebugKit.forceEnable', function () {
    return $_SERVER['REMOTE_ADDR'] === '192.168.2.182';
});
```

* `DebugKit.ignorePathsPattern` skips requests whose URL matches a regex:

```php
Configure::write('DebugKit.ignorePathsPattern', '/\.(jpg|png|gif)$/');
```

* `DebugKit.ignoreAuthorization` tells DebugKit to ignore the Cake Authorization plugin for toolbar requests. It is disabled by default.
* `DebugKit.maxDepth` controls how many levels of nested data are rendered in general debug output. The default is `5`.
* `DebugKit.variablesPanelMaxDepth` controls how many levels of nested data are rendered in the Variables panel. The default is `5`.

Increasing either depth value can cause out-of-memory errors:

```php
Configure::write('DebugKit.maxDepth', 8);
Configure::write('DebugKit.variablesPanelMaxDepth', 8);
```

## Database Configuration

By default DebugKit stores panel data in a SQLite database in your application's `tmp` directory. If `pdo_sqlite` is unavailable, define a `debug_kit` connection in `config/app.php`:

```php
'debug_kit' => [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Mysql',
    'persistent' => false,
    'host' => 'localhost',
    //'port' => 'nonstandard_port_number',
    'username' => 'dbusername',
    'password' => 'dbpassword',
    'database' => 'debug_kit',
    'encoding' => 'utf8',
    'timezone' => 'UTC',
    'cacheMetadata' => true,
    'quoteIdentifiers' => false,
    //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
],
```

You can safely remove `tmp/debug_kit.sqlite` at any time. DebugKit will recreate it as needed.

# Helper Functions

DebugKit includes a few helpers for investigating queries and timing custom code paths.

## SQL Helpers

* `sql()` dumps the SQL for an ORM query.
* `sqld()` dumps the SQL for an ORM query and stops execution.

## Using DebugTimer

Use `DebugKit\DebugTimer` when you want to measure code paths that are not covered by the default timers, such as application services, controller branches, or view rendering:

```php
use DebugKit\DebugTimer;

public function view($id)
{
    DebugTimer::start('load_article', 'Fetching article from database');
    // Code to measure.
    DebugTimer::stop('load_article');
}
```

Completed custom timers appear in the Timer panel for the request.

## Tracing Query Execution

When you need to know where a query originated, add `SqlTraceTrait` to a table class:

```php
use DebugKit\Model\Table\SqlTraceTrait;

class CategoriesTable extends Table
{
    use SqlTraceTrait;
}
```

DebugKit adds location comments to the SQL log:

```text
/* APP/Controller/CategoriesController.php (line 20) */
```

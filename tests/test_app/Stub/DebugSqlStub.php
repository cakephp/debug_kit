<?php
declare(strict_types=1);

namespace DebugKit\TestApp\Stub;

use DebugKit\DebugSql;

class DebugSqlStub extends DebugSql
{
    public static $isCli = true;

    protected static function isCli()
    {
        return static::$isCli;
    }
}

<?php
namespace DebugKit;

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\ORM\Query;
use SqlFormatter;

/**
 * Contains methods for dumping well formatted SQL queries.
 */
class DebugSql
{
    /**
     * @var string
     */
    private static $template = '<div class="cake-debug-output">%s<pre class="cake-debug">%s</pre></div>';

    /**
     * @param Query $query
     * @param int $stackDepth
     */
    public static function dsql(Query $query, $stackDepth = 1)
    {
        static::sql($query, $stackDepth);
        die(1);
    }

    /**
     * @param Query $query
     * @param int $stackDepth
     * @return Query
     */
    public static function sql(Query $query, $stackDepth = 0)
    {
        if (!Configure::read('debug')) {
            return $query;
        }

        $sql = (string)$query;

        if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' || !class_exists('SqlFormatter')) {
            dd($sql);
        }

        $trace = Debugger::trace(['start' => 1, 'depth' => $stackDepth + 2, 'format' => 'array']);
        $lineInfo = sprintf(
            '<span><strong>%s</strong> (line <strong>%s</strong>)</span>',
            $trace[$stackDepth]['file'],
            $trace[$stackDepth]['line']
        );

        $formatted = SqlFormatter::format($sql);
        $formatted = str_replace(
            '<span >:</span> <span style="color: #333;">',
            '<span >:</span><span style="color: #333;">',
            $formatted
        );

        printf(static::$template, $lineInfo, $formatted);

        return $query;
    }
}
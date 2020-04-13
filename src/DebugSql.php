<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 3.11.4
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit;

use Cake\Core\Configure;
use Cake\Database\Query;
use Cake\Error\Debugger;
use SqlFormatter;

/**
 * Contains methods for dumping well formatted SQL queries.
 */
class DebugSql
{
    /**
     * Template used for HTML output.
     *
     * @var string
     */
    private static $templateHtml = <<<HTML
<div class="cake-debug-output">
%s
<pre class="cake-debug">
%s
</pre>
</div>
HTML;

    /**
     * Template used for CLI and text output.
     *
     * @var string
     */
    private static $templateText = <<<TEXT
%s
########## DEBUG ##########
%s
###########################

TEXT;

    /**
     * Prints out the SQL statements generated by a Query object.
     *
     * This function returns the same variable that was passed.
     * Only runs if debug mode is enabled.
     *
     * @param \Cake\Database\Query $query Query to show SQL statements for.
     * @param bool $showValues Renders the SQL statement with bound variables.
     * @param bool|null $showHtml If set to true, the method prints the debug
     *    data in a browser-friendly way.
     * @param int $stackDepth Provides a hint as to which file in the call stack to reference.
     * @return \Cake\Database\Query
     */
    public static function sql(Query $query, $showValues = true, $showHtml = null, $stackDepth = 0)
    {
        if (!Configure::read('debug')) {
            return $query;
        }

        $sql = (string)$query;
        if ($showValues) {
            $sql = static::interpolate($sql, $query->getValueBinder()->bindings());
        }

        $trace = Debugger::trace(['start' => 1, 'depth' => $stackDepth + 2, 'format' => 'array']);
        /** @psalm-suppress PossiblyInvalidArrayOffset */
        $file = isset($trace[$stackDepth]) ? $trace[$stackDepth]['file'] : 'n/a';
        /** @psalm-suppress PossiblyInvalidArrayOffset */
        $line = isset($trace[$stackDepth]) ? $trace[$stackDepth]['line'] : 0;
        $lineInfo = '';
        if ($file) {
            $search = [];
            if (defined('ROOT')) {
                $search = [ROOT];
            }
            if (defined('CAKE_CORE_INCLUDE_PATH')) {
                array_unshift($search, CAKE_CORE_INCLUDE_PATH);
            }
            $file = str_replace($search, '', $file);
        }

        $template = static::$templateHtml;
        $sqlHighlight = true;
        if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') || $showHtml === false) {
            $template = static::$templateText;
            $sqlHighlight = false;
            if ($file && $line) {
                $lineInfo = sprintf('%s (line %s)', $file, $line);
            }
        }
        if ($showHtml === null && $template !== static::$templateText) {
            $showHtml = true;
        }

        $var = $showHtml ? SqlFormatter::format($sql, $sqlHighlight) : $sql;
        $var = str_replace(
            '<span >:</span> <span style="color: #333;">',
            '<span >:</span><span style="color: #333;">',
            $var
        );

        if ($showHtml) {
            $template = static::$templateHtml;
            if ($file && $line) {
                $lineInfo = sprintf('<span><strong>%s</strong> (line <strong>%s</strong>)</span>', $file, $line);
            }
        }

        printf($template, $lineInfo, $var);

        return $query;
    }

    /**
     * Prints out the SQL statements generated by a Query object and dies.
     *
     * Only runs if debug mode is enabled.
     * It will otherwise just continue code execution and ignore this function.
     *
     * @param \Cake\Database\Query $query Query to show SQL statements for.
     * @param bool $showValues Renders the SQL statement with bound variables.
     * @param bool|null $showHtml If set to true, the method prints the debug
     *    data in a browser-friendly way.
     * @param int $stackDepth Provides a hint as to which file in the call stack to reference.
     * @return void
     */
    public static function sqld(Query $query, $showValues = true, $showHtml = null, $stackDepth = 1)
    {
        static::sql($query, $showValues, $showHtml, $stackDepth);
        die(1);
    }

    /**
     * Helper function used to replace query placeholders by the real
     * params used to execute the query.
     *
     * @param string $sql The SQL statement
     * @param array $bindings The Query bindings
     * @return string
     */
    private static function interpolate($sql, array $bindings)
    {
        $params = array_map(function ($binding) {
            $p = $binding['value'];

            if ($p === null) {
                return 'NULL';
            }
            if (is_bool($p)) {
                return $p ? '1' : '0';
            }

            if (is_string($p)) {
                $replacements = [
                    '$' => '\\$',
                    '\\' => '\\\\\\\\',
                    "'" => "''",
                ];

                $p = strtr($p, $replacements);

                return "'$p'";
            }

            return $p;
        }, $bindings);

        $keys = [];
        $limit = is_int(key($params)) ? 1 : -1;
        foreach ($params as $key => $param) {
            $keys[] = is_string($key) ? "/$key\b/" : '/[?]/';
        }

        return preg_replace($keys, $params, $sql, $limit);
    }
}

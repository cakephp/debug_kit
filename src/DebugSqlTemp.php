<?php
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
use Cake\Error\Debugger;

/**
 * Contains methods for debugging SQL queries.
 */
class DebugSqlTemp
{
    /**
     * Applies a comment to a query about which file created it.
     *
     * @param \Cake\ORM\Query $query The Query to insert a comment into.
     * @param int $start How many entries in the stack trace to skip.
     * @param bool $debugOnly False to always stamp queries with a comment.
     * @return \Cake\ORM\Query
     */
    public static function fileStamp($query, $start = 1, $debugOnly = true)
    {
        if (!Configure::read('debug') && $debugOnly === true) {
            return $query;
        }

        $traces = Debugger::trace(['start' => $start, 'format' => 'array']);
        $file = '[unknown]';
        $line = '??';

        foreach ($traces as $trace) {
            $path = $trace['file'];
            $line = $trace['line'];
            $file = Debugger::trimPath($path);
            if ($path === '[internal]') {
                continue;
            }
            if (defined('CAKE_CORE_INCLUDE_PATH') && strpos($path, CAKE_CORE_INCLUDE_PATH) !== 0) {
                break;
            }
        }

        return $query->modifier(sprintf('/* %s (line %s) */', $file, $line));
    }
}

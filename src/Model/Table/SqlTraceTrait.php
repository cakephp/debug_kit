<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Model\Table;

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\ORM\Query;

/**
 * This trait modifies a Table class to inject metadata into a Query object that can be used to provide a call track
 * in the SQL log panel.
 *
 * @mixin \Cake\ORM\Table
 */
trait SqlTraceTrait
{
    /**
     * {@inheritDoc}
     */
    public function query()
    {
        /** @var Query $query */
        $query = parent::query();
        if (!Configure::read('debug')) {
            return $query;
        }

        $traces = Debugger::trace(['start' => 2, 'depth' => 3, 'format' => 'array']);
        $file = null;
        $line = null;

        foreach ($traces as $trace) {
            $fullPath = $trace['file'];
            $file = Debugger::trimPath($trace['file']);
            $line = $trace['line'];
            if (defined('CAKE_CORE_INCLUDE_PATH') && strpos($fullPath, CAKE_CORE_INCLUDE_PATH) !== 0) {
                break;
            }
        }

        $comment = sprintf('/* %s (line %s) */', $file, $line);
        $query->epilog($query->newExpr($comment));

        return $query;
    }
}

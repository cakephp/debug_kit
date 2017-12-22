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
 * @since         3.11.5
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Model\Table;

use Cake\Core\Configure;
use Cake\Error\Debugger;

/**
 * Add this trait to your Table class to append the file reference of where a Query object was created.
 *
 * @mixin \Cake\ORM\Table
 */
trait SqlTraceTrait
{
    /**
     * Creates a new Query instance for this repository
     *
     * @return \Cake\ORM\Query
     */
    public function query()
    {
        $query = parent::query();

        if (!Configure::read('debug') || $query->clause('epilog') !== null) {
            return $query;
        }

        $traces = Debugger::trace(['start' => 2, 'depth' => 3, 'format' => 'array']);
        $file = 'n/a';
        $line = 0;

        foreach ($traces as $trace) {
            $path = $trace['file'];
            $line = $trace['line'];
            $file = Debugger::trimPath($path);
            if (defined('CAKE_CORE_INCLUDE_PATH') && strpos($path, CAKE_CORE_INCLUDE_PATH) !== 0) {
                break;
            }
        }

        return $query->epilog($query->newExpr(sprintf('/* %s (line %s) */', $file, $line)));
    }
}

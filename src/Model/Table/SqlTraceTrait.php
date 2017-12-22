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

use DebugKit\DebugSqlTemp;

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
        return DebugSqlTemp::fileStamp(parent::query(), 2);
    }
}

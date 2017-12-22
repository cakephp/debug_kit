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
 * @since         3.11.5
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Model\Table\PanelsTable;

/**
 * Tests for SqlTraceTrait debugging comments.
 */
class SqlTraceTraitTest extends TestCase
{
    /**
     * No epilog should be set when debug is off.
     */
    public function testDebugOff()
    {

    }

    /**
     * Do not overwrite elipog is already set.
     */
    public function testEpilogAlreadySet()
    {
        /** @var PanelsTable $panels */
        $panels = TableRegistry::get('Panels');
    }

    /**
     * Verify file name when calling find()
     */
    public function testFind()
    {

    }

    /**
     * Verify file name when calling query()
     */
    public function testQuery()
    {

    }

    /**
     * Verify file name is correct when the table object calls the query() method on itself.
     */
    public function testShortCallStack()
    {

    }

    /**
     * Verify file name when calling update()
     */
    public function testUpdate()
    {

    }
}

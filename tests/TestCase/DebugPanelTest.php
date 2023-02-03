<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase;

use Cake\TestSuite\TestCase;
use DebugKit\DebugPanel;

/**
 * Testing stub.
 */
class SimplePanel extends DebugPanel
{
}

/**
 * DebugPanel TestCase
 */
class DebugPanelTest extends TestCase
{
    /**
     * @var SimplePanel
     */
    protected $panel;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->panel = new SimplePanel();
    }

    public function testTitle()
    {
        $this->assertEquals('Simple', $this->panel->title());
    }

    public function testElementName()
    {
        $this->assertEquals('DebugKit.simple_panel', $this->panel->elementName());

        $this->panel->plugin = 'Derpy';
        $this->assertEquals('Derpy.simple_panel', $this->panel->elementName());

        $this->panel->plugin = false;
        $this->assertEquals('simple_panel', $this->panel->elementName());
    }
}

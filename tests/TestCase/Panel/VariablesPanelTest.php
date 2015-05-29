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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\Form\Form;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\VariablesPanel;

/**
 * Class VariablesPanelTest
 *
 */
class VariablesPanelTest extends TestCase
{
    public $fixtures = ['plugin.debug_kit.requests', 'plugin.debug_kit.panels'];

    /**
     * set up
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->panel = new VariablesPanel();
    }

    /**
     * Teardown method.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->panel);
    }

    /**
     * test shutdown
     *
     * @return void
     */
    public function testShutdown()
    {
        $requests = TableRegistry::get('Requests');
        $query = $requests->find('all');
        $result = $requests->find()->all();

        $controller = new \StdClass();
        $controller->viewVars = [
            'query' => $query,
            'result set' => $result,
            'string' => 'yes',
            'array' => ['some' => 'key']
        ];
        $event = new Event('Controller.shutdown', $controller);
        $this->panel->shutdown($event);
        $output = $this->panel->data();

        $this->assertInstanceOf(
            'Cake\ORM\Query',
            $controller->viewVars['query'],
            'Original value should not be mutated'
        );
        $this->assertInternalType('array', $output['content']['query']);
        $this->assertInternalType('array', $output['content']['result set']);
        $this->assertEquals($controller->viewVars['string'], $output['content']['string']);
        $this->assertEquals($controller->viewVars['array'], $output['content']['array']);
    }
}

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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\TestSuite\TestCase;
use Cake\View\ViewVarsTrait;
use DebugKit\Panel\VariablesPanel;
use DebugKit\TestApp\Form\TestForm;

/**
 * Class VariablesPanelTest
 */
class VariablesPanelTest extends TestCase
{
    /**
     * @var VariablesPanel
     */
    protected $panel;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->panel = new VariablesPanel();
    }

    /**
     * Teardown method.
     *
     * @return void
     */
    public function tearDown(): void
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
        $requests = $this->getTableLocator()->get('Requests');
        $query = $requests->find('all');
        $result = $requests->find()->all();
        $unbufferedQuery = $requests->find('all')->enableBufferedResults(false);
        $unbufferedQuery->toArray(); //toArray call would normally happen somewhere in View, usually implicitly
        $update = $requests->query()->update();
        $debugInfoException = $requests->query()->contain('NonExistentAssociation');

        $unserializable = new \stdClass();
        $unserializable->pdo = $requests->getConnection()->getDriver()->getConnection();

        $unserializableDebugInfo = $this
            ->getMockBuilder('\stdClass')
            ->addMethods(['__debugInfo'])
            ->getMock();
        $unserializableDebugInfo->expects($this->any())->method('__debugInfo')->willReturn([
            'unserializable' => $unserializable,
        ]);

        $resource = fopen('data:text/plain;base64,', 'r');

        $controller = new class {
            use ViewVarsTrait;
        };
        $vars = [
            'resource' => $resource,
            'unserializableDebugInfo' => $unserializableDebugInfo,
            'debugInfoException' => $debugInfoException,
            'updateQuery' => $update,
            'query' => $query,
            'unbufferedQuery' => $unbufferedQuery,
            'result set' => $result,
            'string' => 'yes',
            'array' => ['some' => 'key'],
            'notSerializableForm' => new TestForm(),
        ];
        $controller->viewBuilder()->setVars($vars);
        $event = new Event('Controller.shutdown', $controller);
        $this->panel->shutdown($event);
        $output = $this->panel->data();

        $this->assertIsArray($output);
        $this->assertEquals(array_keys($vars), array_keys($output['variables']));
    }
}

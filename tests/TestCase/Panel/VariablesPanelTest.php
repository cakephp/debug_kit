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
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\View\ViewVarsTrait;
use DebugKit\Panel\VariablesPanel;
use DebugKit\TestApp\Form\TestForm;

/**
 * Class VariablesPanelTest
 *
 */
class VariablesPanelTest extends TestCase
{
    public $fixtures = ['plugin.DebugKit.Requests', 'plugin.DebugKit.Panels'];

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
        $requests = TableRegistry::get('Requests');
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
            ->setMethods(['__debugInfo'])
            ->getMock();
        $unserializableDebugInfo->expects($this->once())->method('__debugInfo')->willReturn([
            'unserializable' => $unserializable,
        ]);

        $resource = fopen('data:text/plain;base64,', 'r');

        $controller = new class {
            use ViewVarsTrait;
        };
        $controller->viewBuilder()->setVars([
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
        ]);
        $event = new Event('Controller.shutdown', $controller);
        $this->panel->shutdown($event);
        $output = $this->panel->data();

        array_walk_recursive($output, function ($item) {
            try {
                serialize($item);
            } catch (\Exception $e) {
                $this->fail('Panel Output content is not serializable');
            }
        });
        $this->assertRegExp('/^\[stream\] Resource id #\d+$/', $output['content']['resource']);
        $this->assertIsArray($output['content']['unserializableDebugInfo']);

        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $expectedErrorMessage = "Unserializable object - stdClass. Error: Serialization of 'PDO' is not allowed";
        } else {
            $expectedErrorMessage = 'Unserializable object - stdClass. Error: You cannot serialize or unserialize PDO instances';
        }
        $this->assertStringStartsWith(
            $expectedErrorMessage,
            $output['content']['unserializableDebugInfo']['unserializable']
        );
        $this->assertStringStartsWith(
            'Could not retrieve debug info - Cake\ORM\Query. Error: The NonExistentAssociation association is not defined on Requests',
            $output['content']['debugInfoException']
        );
        $this->assertInstanceOf(
            'Cake\ORM\Query',
            $controller->viewBuilder()->getVar('query'),
            'Original value should not be mutated'
        );
        $this->assertIsArray($output['content']['updateQuery']);
        $this->assertIsArray($output['content']['query']);
        $this->assertIsArray($output['content']['unbufferedQuery']);
        $this->assertIsArray($output['content']['result set']);
        $this->assertEquals($controller->viewBuilder()->getVar('string'), $output['content']['string']);
        $this->assertEquals($controller->viewBuilder()->getVar('array'), $output['content']['array']);
    }
}

<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\DebugKit\Test\Routing\Filter;

use Cake\DebugKit\Routing\Filter\DebugBarFilter;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Test the debug bar
 */
class DebugBarFilterTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = ['plugin.debug_kit.request', 'plugin.debug_kit.panel'];

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->events = new EventManager();
	}

/**
 * Test loading panels.
 *
 * @return void
 */
	public function testSetupLoadingPanels() {
		$bar = new DebugBarFilter($this->events, []);
		$bar->setup();

		$this->assertContains('SqlLog', $bar->loadedPanels());
		$this->assertCount(1, $this->events->listeners('Controller.shutdown'));
		$this->assertInstanceOf('Cake\DebugKit\Panel\SqlLogPanel', $bar->panel('SqlLog'));
	}

/**
 * Test that beforeDispatch sets properties up.
 *
 * @return void
 */
	public function testBeforeDispatch() {
		$request = new Request();
		$event = new Event('Dispatcher.beforeDispatch', $this, compact('request'));

		$bar = new DebugBarFilter($this->events, []);
		$bar->beforeDispatch($event);
		$this->assertArrayHasKey('_debug_kit_id', $request->params);
	}

/**
 * Test that afterDispatch saves panel data.
 *
 * @return void
 */
	public function testAfterDispatchSavesData() {
		$request = new Request(['url' => '/articles']);
		$request->params['_debug_kit_id'] = 'abc123';

		$bar = new DebugBarFilter($this->events, []);
		$bar->setup();

		$event = new Event('Dispatcher.afterDispatch', $this, compact('request'));
		$bar->afterDispatch($event);

		$requests = TableRegistry::get('DebugKit.Requests');
		$result = $requests->find()->contain('Panels')->first();

		$this->assertEquals('articles', $result->url);
		$this->assertNotEmpty($result->requested_at);
		$this->assertCount(1, $result->panels);
		$this->assertEquals('SqlLog', $result->panels[0]->panel);
	}

}

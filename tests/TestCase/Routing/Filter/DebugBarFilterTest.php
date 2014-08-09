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
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\String;

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
 * Test that afterDispatch saves panel data.
 *
 * @return void
 */
	public function testAfterDispatchSavesData() {
		$request = new Request(['url' => '/articles']);
		$request->params['_debug_kit_id'] = String::uuid();

		$response = new Response(['statusCode' => 200, 'type' => 'text/html']);

		$bar = new DebugBarFilter($this->events, []);
		$bar->setup();

		$event = new Event('Dispatcher.afterDispatch', $this, compact('request', 'response'));
		$bar->afterDispatch($event);

		$requests = TableRegistry::get('DebugKit.Requests');
		$result = $requests->find()->contain('Panels')->first();

		$this->assertEquals('articles', $result->url);
		$this->assertNotEmpty($result->requested_at);
		$this->assertNotEmpty('text/html', $result->content_type);
		$this->assertEquals(200, $result->status_code);
		$this->assertCount(1, $result->panels);

		$this->assertEquals('SqlLog', $result->panels[0]->panel);
		$this->assertEquals('DebugKit.sql_log_panel', $result->panels[0]->element);
		$this->assertEquals('Sql Log', $result->panels[0]->title);
	}

/**
 * Test that afterDispatch modifies response
 *
 * @return void
 */
	public function testAfterDispatchModifiesResponse() {
		$request = new Request(['url' => '/articles']);

		$response = new Response([
			'statusCode' => 200,
			'type' => 'text/html',
			'body' => '<html><title>test</title><body><p>some text</p></body>'
		]);

		$bar = new DebugBarFilter($this->events, []);
		$bar->setup();

		$event = new Event('Dispatcher.afterDispatch', $this, compact('request', 'response'));
		$bar->afterDispatch($event);
		$toolbar = TableRegistry::get('DebugKit.Requests')->find()->first();

		$expected = '<html><title>test</title><body><p>some text</p>' .
			"<script>var __debug_kit_id = '" . $toolbar->id . "';</script>" .
			'<script src="/debug_kit/js/toolbar.js"></script>' .
			'</body>';
		$this->assertTextEquals($expected, $response->body());
	}

/**
 * Test that afterDispatch does not modify response
 *
 * @return void
 */
	public function testAfterDispatchNoModifyResponse() {
		$request = new Request(['url' => '/articles']);
		$request->params['_debug_kit_id'] = String::uuid();

		$response = new Response([
			'statusCode' => 200,
			'type' => 'application/json',
			'body' => '{"some":"json"}'
		]);
		$request->params['_debug_kit_id'] = String::uuid();

		$bar = new DebugBarFilter($this->events, []);
		$bar->setup();

		$event = new Event('Dispatcher.afterDispatch', $this, compact('request', 'response'));
		$bar->afterDispatch($event);
		$this->assertTextEquals('{"some":"json"}', $response->body());
	}

}

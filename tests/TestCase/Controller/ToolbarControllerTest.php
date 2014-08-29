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
 * @since         DebugKit 3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Routing\Router;
use Cake\TestSuite\ControllerTestCase;

/**
 * Toolbar controller test.
 */
class ToolbarControllerTestCase extends ControllerTestCase {

/**
 * Fixtures.
 *
 * @var array
 */
	public $fixtures = [
		'plugin.debug_kit.request',
		'plugin.debug_kit.panel'
	];

/**
 * Don't reload routes.
 *
 * @var bool
 */
	public $loadRoutes = false;

/**
 * Setup method.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Router::plugin('DebugKit', function($routes) {
			$routes->connect(
				'/toolbar/clear_cache/*',
				['plugin' => 'DebugKit', 'controller' => 'Toolbar', 'action' => 'clearCache']);
		});
	}

/**
 * Test clearing the cache does not work with GET
 *
 * @expectedException Cake\Error\MethodNotAllowedException
 * @return void
 */
	public function testClearCacheNoGet() {
		$this->testAction('/debug_kit/toolbar/clear_cache', [
			'method' => 'GET',
			'query' => ['name' => 'testing'],
			'return' => 'contents'
		]);
	}

/**
 * Test clearing the cache.
 *
 * @return void
 */
	public function testClearCache() {
		$mock = $this->getMock('Cake\Cache\CacheEngine');
		$mock->expects($this->once())
			->method('init')
			->will($this->returnValue(true));
		$mock->expects($this->once())
			->method('clear')
			->will($this->returnValue(true));
		Cache::config('testing', $mock);

		$result = $this->testAction('/debug_kit/toolbar/clear_cache', [
			'method' => 'POST',
			'data' => ['name' => 'testing'],
			'return' => 'contents'
		]);
		$this->assertContains('success', $result);
	}

}

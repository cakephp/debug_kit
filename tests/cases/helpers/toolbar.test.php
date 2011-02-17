<?php
/**
 * Toolbar facade tests.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.tests.views.helpers
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Helper', array('DebugKit.Toolbar'));
App::import('Core', array('View', 'Controller'));

class MockBackendHelper extends Helper {
}

class ToolbarHelperTestCase extends CakeTestCase {

	public $fixtures = array('core.post');
/**
 * setUp
 *
 * @return void
 **/
	public function setUp() {
		Configure::write('Cache.disable', false);
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');

		$this->Controller = new Controller(null);
		$this->View = new View($this->Controller);
		$this->Toolbar = new ToolbarHelper($this->View, array(
			'output' => 'MockBackendHelper',
			'cacheKey' => 'debug_kit_toolbar_test_case',
			'cacheConfig' => 'default'
		));
		$this->Toolbar->MockBackend = $this->getMock('Helper', array('testMethod'), array($this->View));

		if (isset($this->_debug)) {
			Configure::write('debug', $this->_debug);
		}
	}
/**
 * start Case - switch view paths
 *
 * @return void
 **/
	public function startTest() {
		$this->_viewPaths = App::path('views');
		App::build(array(
			'views' => array(
				TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
				APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
				ROOT . DS . LIBS . 'view' . DS
		)), true);
		$this->_debug = Configure::read('debug');
	}
/**
 * test cache writing for views.
 *
 * @return void
 **/
	public function testCacheWrite() {
		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertTrue($result);
	}
/**
 * Ensure that the cache writing only affects the 
 * top most level of the history stack. As this is where the current request is stored.
 *
 * @return void
 **/
	public function testOnlyWritingToFirstElement() {
		$values = array(
			array('test' => array('content' => array('first', 'values'))),
			array('test' => array('content' => array('second', 'values'))),
		);
		Cache::write('debug_kit_toolbar_test_case', $values, 'default');
		$this->Toolbar->writeCache('test', array('new', 'values'));

		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('new', 'values'));

		$result = $this->Toolbar->readCache('test', 1);
		$this->assertEqual($result, array('second', 'values'));
	}
/**
 * test cache reading for views
 *
 * @return void
 **/
	public function testCacheRead() {
		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertTrue($result, 'Cache write failed %s');
		
		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('stuff', 'to', 'cache'), 'Cache value is wrong %s');
		
		$result = $this->Toolbar->writeCache('test', array('new', 'stuff'));
		$this->assertTrue($result, 'Cache write failed %s');
		
		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('new', 'stuff'), 'Cache value is wrong %s');
	}
/**
 * Test that reading/writing doesn't work with no cache config.
 *
 * @return void
 **/
	public function testNoCacheConfigPresent() {
		$this->Toolbar = new ToolbarHelper($this->View, array('output' => 'MockBackendHelper'));

		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertFalse($result, 'Writing to cache succeeded with no cache config %s');

		$result = $this->Toolbar->readCache('test');
		$this->assertFalse($result, 'Reading cache succeeded with no cache config %s');
	}
/**
 * ensure that getQueryLogs works and writes to the cache so the history panel will 
 * work.
 *
 * @return void
 */
	public function testGetQueryLogs() {
		$model = new CakeTestModel(array('table' => 'posts', 'alias' => 'Post'));
		$model->find('all');
		$model->find('first');

		$result = $this->Toolbar->getQueryLogs($model->useDbConfig, array('cache' => false));
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) >= 2, 'Should be more than 2 queries in the log %s');
		$this->assertTrue(isset($result[0]['actions']));

		$model->find('first');
		Cache::delete('debug_kit_toolbar_test_case', 'default');
		$result = $this->Toolbar->getQueryLogs($model->useDbConfig, array('cache' => true));

		$cached = $this->Toolbar->readCache('sql_log');
		$this->assertTrue(isset($cached[$model->useDbConfig]));
		$this->assertEqual($cached[$model->useDbConfig][0], $result[0]);
	}
/**
 * reset the view paths
 *
 * @return void
 **/
	public function endTest() {
		App::build(array('views' => $this->_viewPaths), true);
		Cache::delete('debug_kit_toolbar_test_case', 'default');
		ClassRegistry::flush();
	}
/**
 * tearDown
 *
 * @access public
 * @return void
 */
	public function tearDown() {
		unset($this->Toolbar, $this->Controller);
	}
}

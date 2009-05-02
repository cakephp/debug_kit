<?php
/**
 * DebugView test Case
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.tests.views
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Core', 'View');

if (!class_exists('DoppelGangerView')) {
	class DoppelGangerView extends View {}
}

App::import('View', 'DebugKit.Debug');
App::import('Vendor', 'DebugKit.DebugKitDebugger');
/**
 * Debug View Test Case
 *
 * @package       debug_kit.tests
 */
class DebugViewTestCase extends CakeTestCase {
/**
 * set Up test case
 *
 * @return void
 **/
	function startTest() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		$this->Controller =& ClassRegistry::init('Controller');
		$this->View  =& new DebugView($this->Controller, false);
		$this->_debug = Configure::read('debug');
		$this->_paths = array();
		$this->_paths['plugin'] = Configure::read('pluginPaths');
		$this->_paths['view'] = Configure::read('viewPaths');
		$this->_paths['vendor'] = Configure::read('vendorPaths');
		$this->_paths['controller'] = Configure::read('controllerPaths');
	}
/**
 * tear down function
 *
 * @return void
 **/
	function endTest() {
		Configure::write('pluginPaths', $this->_paths['plugin']);
		Configure::write('viewPaths', $this->_paths['view']);
		Configure::write('vendorPaths', $this->_paths['vendor']);
		Configure::write('controllerPaths', $this->_paths['controller']);

		unset($this->View, $this->Controller);
		DebugKitDebugger::clearTimers();
		Configure::write('debug', $this->_debug);
	}
	
/**
 * start Case - switch view paths
 *
 * @return void
 **/
	function startCase() {
		$this->_viewPaths = Configure::read('viewPaths');
		Configure::write('viewPaths', array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		));
	}
	
/**
 * test that element timers are working
 *
 * @return void
 **/
	function testElementTimers() {
		$result = $this->View->element('test_element');
		$this->assertPattern('/^this is the test element$/', $result);

		$result = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($result['render_test_element.ctp']));
	}

/**
 * test rendering and ensure that timers are being set.
 *
 * @access public
 * @return void
 */
	function testRenderTimers() {
		$this->Controller->viewPath = 'posts';
		$this->Controller->action = 'index';
		$this->Controller->params = array(
			'action' => 'index',
			'controller' => 'posts',
			'plugin' => null,
			'url' => array('url' => 'posts/index'),
			'base' => null,
			'here' => '/posts/index',
		);
		$this->Controller->layout = 'default';
		$View =& new DebugView($this->Controller, false);
		$View->render('index');
		
		$result = DebugKitDebugger::getTimers();
		$this->assertEqual(count($result), 3);
		$this->assertTrue(isset($result['viewRender']));
		$this->assertTrue(isset($result['render_default.ctp']));
		$this->assertTrue(isset($result['render_index.ctp']));
	}
	
/**
 * Test for correct loading of helpers into custom view
 *
 * @return void
 */
	function testLoadHelpers() {
		$loaded = array();
		$result = $this->View->_loadHelpers($loaded, array('Html', 'Javascript', 'Number'));
		$this->assertTrue(is_object($result['Html']));
		$this->assertTrue(is_object($result['Javascript']));
		$this->assertTrue(is_object($result['Number']));
	}
/**
 * test that $out is returned when a layout is rendered instead of the empty
 * $this->output.  As this causes issues with requestAction()
 *
 * @return void
 **/
	function testProperReturnUnderRequestAction() {
		$plugins = Configure::read('pluginPaths');
		$views = Configure::read('viewPaths');
		$testapp = $plugins[1] . 'debug_kit' . DS . 'tests' . DS . 'test_app' . DS . 'views' . DS;
		array_unshift($views, $testapp);
		Configure::write('viewPaths', $views);
		
		$this->View->set('test', 'I have been rendered.');
		$this->View->viewPath = 'debug_kit_test';
		$this->View->layout = false;
		$result = $this->View->render('request_action_render');

		$this->assertEqual($result, 'I have been rendered.');
	}
}
?>
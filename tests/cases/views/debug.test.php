<?php
/**
 * DebugView test Case
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
		$this->Controller =& new Controller();
		$this->View =& new DebugView($this->Controller, false);
		$this->_debug = Configure::read('debug');
		$this->_paths = array();
		$this->_paths['plugins'] = App::path('plugins');
		$this->_paths['views'] = App::path('views');
		$this->_paths['vendors'] = App::path('vendors');
		$this->_paths['controllers'] = App::path('controllers');
	}
/**
 * tear down function
 *
 * @return void
 **/
	function endTest() {
		App::build(array(
			'plugins' => $this->_paths['plugins'],
			'views' => $this->_paths['views'],
			'vendors' => $this->_paths['vendors'],
			'controllers' => $this->_paths['controllers']
		));

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
		App::build(array(
			'views' => array(
				TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
				APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS,
				ROOT . DS . LIBS . 'view' . DS
			)
		), true);
	}
/**
 * test that element timers are working
 *
 * @return void
 **/
	function testElementTimers() {
		$result = $this->View->element('test_element');
		$expected = <<<TEXT
<!-- Starting to render - test_element -->
this is the test element
<!-- Finished - test_element -->

TEXT;
		$this->assertEqual($result, $expected);

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
		$this->assertEqual(count($result), 4);
		$this->assertTrue(isset($result['viewRender']));
		$this->assertTrue(isset($result['render_default.ctp']));
		$this->assertTrue(isset($result['render_index.ctp']));
		
		$result = DebugKitDebugger::getMemoryPoints();
		$this->assertTrue(isset($result['View render complete']));
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
		$testapp = App::pluginPath('DebugKit') . 'tests' . DS . 'test_app' . DS . 'views' . DS;
		App::build(array('views' => array($testapp)));

		$this->View->set('test', 'I have been rendered.');
		$this->View->action = 'request_action_render';
		$this->View->name = 'DebugKitTest';
		$this->View->viewPath = 'debug_kit_test';
		$this->View->layout = false;
		$result = $this->View->render('request_action_render');

		$this->assertEqual($result, 'I have been rendered.');
	}
}

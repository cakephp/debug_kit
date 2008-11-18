<?php
/* SVN FILE: $Id$ */
/**
 * DebugView test Case
 *
 * 
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.cake.libs.
 * @since         CakePHP v 1.2.0.4487
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
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
	function setUp() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		$this->Controller =& ClassRegistry::init('Controller');
		$this->View  =& new DebugView($this->Controller, false);
		$this->_debug = Configure::read('debug');
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
 * reset the view paths
 *
 * @return void
 **/
	function endCase() {
		Configure::write('viewPaths', $this->_viewPaths);
	}

/**
 * tear down function
 *
 * @return void
 **/
	function tearDown() {
		unset($this->View, $this->Controller);
		DebugKitDebugger::clearTimers();
		Configure::write('debug', $this->_debug);
	}
}
?>
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
 * @copyright		Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link			http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.
 * @since			CakePHP v 1.2.0.4487
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Core', array('View', 'Controller'));
App::import('View', 'DebugKit.DebugView');
App::import('Vendor', 'DebugKit.DebugKitDebugger');
/**
 * Debug View Test Case
 *
 * @package debug_kit.tests
 */
class DebugViewTestCase extends CakeTestCase {
/**
 * set Up test case
 *
 * @return void
 **/
	function setUp() {
		$this->Controller =& new Controller();
		$this->View =& new DebugView($this->Controller, false);
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
		$this->assertEqual(count($result), 4);
		$this->assertTrue(isset($result['viewRender']));
		$this->assertTrue(isset($result['render_default.ctp']));
		$this->assertTrue(isset($result['render_index.ctp']));
	}
	
/**
 * Test injection of toolbar
 *
 * @return void
 **/
	function testInjectToolbar() {
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
		$result = $View->render('index');
		$result = str_replace(array("\n", "\r"), '', $result);
		$this->assertPattern('#<div id\="debugKitToolbar">.+</div></body>#', $result);
	}

/**
 * test Neat Array formatting
 *
 * @return void
 **/
	function testMakeNeatArray() {
		$in = array('key' => 'value');
		$result = $this->View->makeNeatArray($in);
		$expected = array(
			'dl' => array('class' => 'neat-array'),
			'<dt', 'key', '/dt',
			'<dd', 'value', '/dd',
			'/dl'
		);
		$this->assertTags($result, $expected);
		
		$in = array('key' => 'value', 'foo' => 'bar');
		$result = $this->View->makeNeatArray($in);
		$expected = array(
			'dl' => array('class' => 'neat-array'),
			'<dt', 'key', '/dt',
			'<dd', 'value', '/dd',
			'<dt', 'foo', '/dt',
			'<dd', 'bar', '/dd',
			'/dl'
		);
		$this->assertTags($result, $expected);
		
		$in = array(
			'key' => 'value', 
			'foo' => array(
				'this' => 'deep',
				'another' => 'value'
			)
		);
		$result = $this->View->makeNeatArray($in);
		$expected = array(
			'dl' => array('class' => 'neat-array'),
			'<dt', 'key', '/dt',
			'<dd', 'value', '/dd',
			'<dt', 'foo', '/dt',
			'<dd', 
				array('dl' => array('class' => 'neat-array')),
				'<dt', 'this', '/dt',
				'<dd', 'deep', '/dd',
				'<dt', 'another', '/dt',
				'<dd', 'value', '/dd',
				'/dl',
			'/dd',
			'/dl'
		);
		$this->assertTags($result, $expected);
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
	}
}
?>
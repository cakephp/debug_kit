<?php
/* SVN FILE: $Id$ */
/**
 * DebugToolbar Test
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
App::import('Component', 'DebugKit.Toolbar');

class TestToolbarComponent extends ToolbarComponent {

	function loadPanels($panels) {
		$this->_loadPanels($panels);
	}

}

Mock::generate('DebugPanel');

/**
* DebugToolbar Test case
*/
class DebugToolbarTestCase extends CakeTestCase {
	
	function setUp() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		$this->Controller =& ClassRegistry::init('Controller');
		$this->Controller->Component =& ClassRegistry::init('Component');
		$this->Controller->Toolbar =& ClassRegistry::init('TestToolBarComponent', 'Component');
	}

/**
 * test Loading of panel classes
 *
 * @return void
 **/
	function testLoadPanels() {
		$this->Controller->Toolbar->loadPanels(array('session', 'request'));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['session'], 'SessionPanel'));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['request'], 'RequestPanel'));

		$this->expectError();
		$this->Controller->Toolbar->loadPanels(array('randomNonExisting', 'request'));
	}
	
/**
 * test loading of vendor panels from test_app folder
 *
 * @access public
 * @return void
 */
	function testVendorPanels() {
		$_back = Configure::read('vendorPaths');
		Configure::write('vendorPaths', array(APP . 'plugins' . DS . 'debug_kit' . DS . 'tests' . DS . 'test_app' . DS . 'vendors' . DS));
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'panels' => array('test'),
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->assertTrue(isset($this->Controller->Toolbar->panels['test']));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['test'], 'TestPanel'));

		Configure::write('vendorPaths', $_back);
	}

/**
 * test initialize
 *
 * @return void
 * @access public
 **/
	function testInitialize() {
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);

		$this->assertFalse(empty($this->Controller->Toolbar->panels));

		$timers = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($timers['componentInit']));
	}
	
/**
 * test startup
 *
 * @return void
 **/
	function testStartup() {
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'panels' => array('MockDebug')
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Toolbar->panels['MockDebug']->expectOnce('startup');
		$this->Controller->Toolbar->startup($this->Controller);

		$this->assertEqual(count($this->Controller->Toolbar->panels), 1);
		$this->assertTrue(isset($this->Controller->helpers['DebugKit.Toolbar']));
		$this->assertEqual($this->Controller->helpers['DebugKit.Toolbar'], array('output' => 'DebugKit.HtmlToolbar'));

		$timers = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($timers['controllerAction']));
	}

/**
 * Test Before Render callback
 *
 * @return void
 **/
	function testBeforeRender() {
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'panels' => array('MockDebug', 'session')
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Toolbar->panels['MockDebug']->expectOnce('beforeRender');
		$this->Controller->Toolbar->beforeRender($this->Controller);
		
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarPanels']));
		$vars = $this->Controller->viewVars['debugToolbarPanels'];

		$expected = array(
			'plugin' => 'debug_kit',
			'elementName' => 'session_panel',
			'content' => $this->Controller->Session->read(),
			'disableTimer' => true,
		);
		$this->assertEqual($expected, $vars['session']);
	}

/**
 * test alternate javascript library use
 *
 * @return void
 **/
	function testAlternateJavascript() {
		$this->Controller->components = array(
			'DebugKit.Toolbar'
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => '/debug_kit/js/js_debug_toolbar',
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);
		
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => 'jquery',
			),
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => '/debug_kit/js/jquery_debug_toolbar.js',
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);


		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => false
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array();
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);
		

		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => array('my_library'),
			),
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => 'my_library_debug_toolbar'
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);

		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => array('/my/path/to/file')
			),
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => '/my/path/to/file',
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);

		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => '/js/custom_behavior',
			),
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => '/js/custom_behavior',
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);
	}
/**
 * Test alternate javascript existing in the plugin.
 *
 * @return void
 **/
	function testExistingAlterateJavascript() {
		$filename = APP . 'plugins' . DS . 'debug_kit' . DS . 'vendors' . DS . 'js' . DS . 'test_alternate_debug_toolbar.js';
		$this->skipIf(!is_writable(dirname($filename)), 'Skipping existing javascript test, debug_kit/vendors/js must be writable');
		
		@touch($filename);
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'javascript' => 'test_alternate',
			),
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarJavascript']));
		$expected = array(
			'behavior' => '/debug_kit/js/test_alternate_debug_toolbar.js',
		);
		$this->assertEqual($this->Controller->viewVars['debugToolbarJavascript'], $expected);
		@unlink($filename);
	}
/**
 * test the Log panel log reading.
 *
 * @return void
 **/
	function testLogPanel() {
		usleep(20);
		$this->Controller->log('This is a log I made this request');
		$this->Controller->log('This is the second  log I made this request');
		$this->Controller->log('This time in the debug log!', LOG_DEBUG);
		
		$this->Controller->components = array(
			'DebugKit.Toolbar' => array(
				'panels' => array('log', 'session')
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$result = $this->Controller->viewVars['debugToolbarPanels']['log'];
		
		$this->assertEqual(count($result['content']), 2);
		$this->assertEqual(count($result['content']['error.log']), 4);
		$this->assertEqual(count($result['content']['debug.log']), 2);
		
		$this->assertEqual(trim($result['content']['debug.log'][1]), 'Debug: This time in the debug log!');
		$this->assertEqual(trim($result['content']['error.log'][1]), 'Error: This is a log I made this request');
	}


/**
 * teardown
 *
 * @return void
 **/
	function tearDown() {
		unset($this->Controller);
		if (class_exists('DebugKitDebugger')) {
			DebugKitDebugger::clearTimers();
		}
	}
}
?>
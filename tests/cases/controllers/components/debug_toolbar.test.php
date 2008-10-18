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
App::import('Core', array('Controller', 'Component'));
App::import('Component', 'DebugKit.DebugToolbar');

class TestDebugToolbarComponent extends DebugToolbarComponent {

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
		$this->Controller =& new Controller();
		$this->Controller->Component =& new Component();
		$this->Controller->DebugToolbar =& new TestDebugToolbarComponent();
	}
	
/**
 * test Loading of panel classes
 *
 * @return void
 **/
	function testLoadPanels() {
		$this->Controller->DebugToolbar->loadPanels(array('session', 'request'));
		$this->assertTrue(is_a($this->Controller->DebugToolbar->panels['session'], 'SessionPanel'));
		$this->assertTrue(is_a($this->Controller->DebugToolbar->panels['request'], 'RequestPanel'));

		$this->expectError();
		$this->Controller->DebugToolbar->loadPanels(array('randomNonExisting', 'request'));
	}
	
/**
 * test initialize
 *
 * @return void
 * @access public
 **/
	function testInitialize() {
		$this->Controller->components = array('DebugKit.DebugToolbar');
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		
		$this->assertFalse(empty($this->Controller->DebugToolbar->panels));

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
			'DebugKit.DebugToolbar' => array(
				'panels' => array('MockDebug')
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->DebugToolbar->panels['MockDebug']->expectOnce('startup');
		$this->Controller->DebugToolbar->startup($this->Controller);

		$this->assertEqual(count($this->Controller->DebugToolbar->panels), 1);
		$this->assertEqual($this->Controller->view, 'DebugKit.Debug');

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
			'DebugKit.DebugToolbar' => array(
				'panels' => array('MockDebug', 'session')
			)
		);
		$this->Controller->Component->init($this->Controller);
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->DebugToolbar->panels['MockDebug']->expectOnce('beforeRender');
		$this->Controller->DebugToolbar->beforeRender($this->Controller);
		
		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarPanels']));
		$vars = $this->Controller->viewVars['debugToolbarPanels'];

		$expected = array(
			'plugin' => 'debugKit',
			'elementName' => 'session_panel',
			'content' => $this->Controller->Session->read(),
		);
		$this->assertEqual($expected, $vars['session']);
	}

/**
 * teardown
 *
 * @return void
 **/
	function tearDown() {
		unset($this->Controller);
		DebugKitDebugger::clearTimers();
	}
}
?>
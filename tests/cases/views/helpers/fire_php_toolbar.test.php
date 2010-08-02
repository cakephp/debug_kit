<?php
/**
 * Toolbar Abstract Helper Test Case
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
$path = App::pluginPath('DebugKit');

App::import('Helper', 'DebugKit.FirePhpToolbar');
App::import('Core', array('View', 'Controller'));
require_once $path . 'tests' . DS . 'cases' . DS . 'test_objects.php';

FireCake::getInstance('TestFireCake');

class FirePhpToolbarHelperTestCase extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 **/
	function startTest() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');

		$this->Toolbar =& new ToolbarHelper(array('output' => 'DebugKit.FirePhpToolbar'));
		$this->Toolbar->FirePhpToolbar =& new FirePhpToolbarHelper();

		$this->Controller =& new Controller();
		if (isset($this->_debug)) {
			Configure::write('debug', $this->_debug);
		}
	}
/**
 * start Case - switch view paths
 *
 * @return void
 **/
	function startCase() {
		$this->_viewPaths = App::build('views');
		App::build(array(
			'views' => array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		)), true);
		$this->_debug = Configure::read('debug');
		$this->firecake =& FireCake::getInstance();
	}
/**
 * test neat array (dump)creation
 *
 * @return void
 */
	function testMakeNeatArray() {
		$this->Toolbar->makeNeatArray(array(1,2,3));
		$result = $this->firecake->sentHeaders;
		$this->assertTrue(isset($result['X-Wf-1-1-1-1']));
		$this->assertPattern('/\[1,2,3\]/', $result['X-Wf-1-1-1-1']);
	}
/**
 * testAfterlayout element rendering
 *
 * @return void
 */
	function testAfterLayout(){
		$this->Controller->viewPath = 'posts';
		$this->Controller->action = 'index';
		$this->Controller->params = array(
			'action' => 'index',
			'controller' => 'posts',
			'plugin' => null,
			'url' => array('url' => 'posts/index', 'ext' => 'xml'),
			'base' => null,
			'here' => '/posts/index',
		);
		$this->Controller->layout = 'default';
		$this->Controller->uses = null;
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$result = $this->Controller->render();
		$this->assertNoPattern('/debug-toolbar/', $result);
		$result = $this->firecake->sentHeaders;
		$this->assertTrue(is_array($result));
	}
/**
 * test starting a panel
 *
 * @return void
 **/
	function testPanelStart() {
		$this->Toolbar->panelStart('My Panel', 'my_panel');
		$result = $this->firecake->sentHeaders;
		$this->assertPattern('/GROUP_START.+My Panel/', $result['X-Wf-1-1-1-1']);
	}
/**
 * test ending a panel
 *
 * @return void
 **/
	function testPanelEnd() {
		$this->Toolbar->panelEnd();
		$result = $this->firecake->sentHeaders;
		$this->assertPattern('/GROUP_END/', $result['X-Wf-1-1-1-1']);	
	}
/**
 * endTest()
 *
 * @return void
 */
	function endTest() {
		TestFireCake::reset();
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
 * tearDown
 *
 * @access public
 * @return void
 */
	function tearDown() {
		unset($this->Toolbar, $this->Controller);
		ClassRegistry::removeObject('view');
		ClassRegistry::flush();
		Router::reload();
	}
}

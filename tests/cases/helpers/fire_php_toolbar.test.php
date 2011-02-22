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
	public function setUp() {
		parent::setUp();

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');

		$this->Controller = new Controller();
		$this->View = new View($this->Controller);
		$this->Toolbar = new ToolbarHelper($this->View, array('output' => 'DebugKit.FirePhpToolbar'));
		$this->Toolbar->FirePhpToolbar = new FirePhpToolbarHelper($this->View);

		$this->firecake = FireCake::getInstance();
	}
/**
 * start test - switch view paths
 *
 * @return void
 **/
	public static function setupBeforeClass() {
		App::build(array(
			'views' => array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		)), true);
	}

/**
 * endTest()
 *
 * @return void
 */
	public static function tearDownAfterClass() {
		App::build();
	}

/**
 * tearDown
 *
 * @access public
 * @return void
 */
	public function tearDown() {
		unset($this->Toolbar, $this->Controller);
		ClassRegistry::flush();
		Router::reload();
		TestFireCake::reset();
	}

/**
 * test neat array (dump)creation
 *
 * @return void
 */
	public function testMakeNeatArray() {
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
	public function testAfterLayout(){
		$this->Controller->viewPath = 'posts';
		$request = new CakeRequest('/posts/index');
		$request->addParams(Router::parse($request->url));
		$request->addPaths(array(
			'webroot' => '/',
			'base' => '/',
			'here' => '/posts/index',
		));
		$this->Controller->setRequest($request);
		$this->Controller->layout = 'default';
		$this->Controller->uses = null;
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->constructClasses();
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
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
	public function testPanelStart() {
		$this->Toolbar->panelStart('My Panel', 'my_panel');
		$result = $this->firecake->sentHeaders;
		$this->assertPattern('/GROUP_START.+My Panel/', $result['X-Wf-1-1-1-1']);
	}
/**
 * test ending a panel
 *
 * @return void
 **/
	public function testPanelEnd() {
		$this->Toolbar->panelEnd();
		$result = $this->firecake->sentHeaders;
		$this->assertPattern('/GROUP_END/', $result['X-Wf-1-1-1-1']);	
	}

}

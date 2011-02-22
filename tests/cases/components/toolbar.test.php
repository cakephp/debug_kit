<?php

/**
 * DebugToolbar Test
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
 * @subpackage    debug_kit.tests.controllers.components
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Component', 'DebugKit.Toolbar');

class TestToolbarComponent extends ToolbarComponent {
	public $evalTest = false;
	public $evalCode = '';

	public function loadPanels($panels, $settings = array()) {
		$this->_loadPanels($panels, $settings);
	}

	protected function _eval($code) {
		if ($this->evalTest) {
			$this->evalCode = $code;
			return;
		}
		eval($code);
	}
}

if (!class_exists('AppController')) {
	class AppController extends Controller {

	}
}


/**
* DebugToolbar Test case
*/
class DebugToolbarTestCase extends CakeTestCase {

/**
 * fixtures.
 *
 * @var array
 **/
	public $fixtures = array('core.article');

/**
 * url for test
 *
 * @var string
 **/
	public $url;

/**
 * Start test callback
 *
 * @access public
 * @return void
 **/
	public function setUp() {
		parent::setUp();

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		$this->_server = $_SERVER;
		$this->_get = $_GET;
		$this->_paths = array();
		$this->_paths['plugins'] = App::path('plugins');
		$this->_paths['views'] = App::path('views');
		$this->_paths['vendors'] = App::path('vendors');
		$this->_paths['controllers'] = App::path('controllers');
		Configure::write('Cache.disable', false);

		$this->url = '/';
	}

/**
 * endTest
 *
 * @return void
 **/
	public function tearDown() {
		parent::tearDown();

		$_SERVER = $this->_server;
		$_GET = $this->_get;
		App::build(array(
			'plugins' => $this->_paths['plugins'],
			'views' => $this->_paths['views'],
			'controllers' => $this->_paths['controllers'],
			'vendors' => $this->_paths['vendors']
		), true);
		Configure::write('Cache.disable', true);

		unset($this->Controller);
		ClassRegistry::flush();
		if (class_exists('DebugKitDebugger')) {
			DebugKitDebugger::clearTimers();
			DebugKitDebugger::clearMemoryPoints();
		}
		Router::reload();
	}
/**
 * loading test controller
 *
 * @return Controller
 **/
	protected function _loadController($settings = array()) {
		$request = new CakeRequest($this->url);
		$request->addParams(Router::parse($this->url));
		$this->Controller = new Controller($request);
		$this->Controller->uses = null;
		$this->Controller->components = array('Toolbar' => $settings + array('className' => 'TestToolbar'));
		$this->Controller->constructClasses();
		$this->Controller->Components->trigger('initialize', array($this->Controller));
		return $this->Controller;
	}

/**
 * test Loading of panel classes
 *
 * @return void
 **/
	public function testLoadPanels() {
		$this->_loadController();

		$this->Controller->Toolbar->loadPanels(array('session', 'request'));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['session'], 'SessionPanel'));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['request'], 'RequestPanel'));

		$this->Controller->Toolbar->loadPanels(array('history'), array('history' => 10));
		$this->assertEqual($this->Controller->Toolbar->panels['history']->history, 10);

		$this->expectError();
		$this->Controller->Toolbar->loadPanels(array('randomNonExisting', 'request'));
	}

/**
 * test Loading of panel classes from a plugin
 *
 * @return void
 **/
	public function testLoadPluginPanels() {
		$debugKitPath = App::pluginPath('DebugKit');
		$noDir = (empty($debugKitPath) || !file_exists($debugKitPath));
		if ($noDir) {
			$this->markTestAsSkipped('Could not find debug_kit in plugin paths');
		}

		App::build(array('plugins' => array($debugKitPath . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)));

		$this->_loadController();
		$this->Controller->Toolbar->loadPanels(array('DebugkitTestPlugin.PluginTest'));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['PluginTest'], 'PluginTestPanel'));
	}

/**
 * test generating a DoppelGangerView with a pluginView.
 *
 * @return void
 **/
	public function testPluginViewParsing() {
		$this->_loadController();

		App::import('Vendor', 'DebugKit.DebugKitDebugger');
		$this->Controller->Toolbar->evalTest = true;
		$this->Controller->viewClass = 'Plugin.OtherView';
		$this->Controller->Toolbar->startup($this->Controller);
		$this->assertPattern('/class DoppelGangerView extends OtherView/', $this->Controller->Toolbar->evalCode);
	}

/**
 * test loading of vendor panels from test_app folder
 *
 * @access public
 * @return void
 */
	public function testVendorPanels() {
		$debugKitPath = App::pluginPath('DebugKit');
		$noDir = (empty($debugKitPath) || !file_exists($debugKitPath));
		if ($noDir) {
			$this->markTestAsSkipped('Could not find debug_kit in plugin paths');
		}

		App::build(array(
			'vendors' => array($debugKitPath . 'tests' . DS . 'test_app' . DS . 'vendors' . DS)
		));
		$this->_loadController(array(
			'panels' => array('test'),
			'className' => 'DebguKit.Toolbar',
		));
		$this->assertTrue(isset($this->Controller->Toolbar->panels['test']));
		$this->assertTrue(is_a($this->Controller->Toolbar->panels['test'], 'TestPanel'));
	}

/**
 * test construct
 *
 * @return void
 * @access public
 **/
	public function testConstruct() {
		$this->_loadController();

		$this->assertFalse(empty($this->Controller->Toolbar->panels));

		$timers = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($timers['componentInit']));
		$memory = DebugKitDebugger::getMemoryPoints();
		$this->assertTrue(isset($memory['Component initialization']));
	}

/**
 * test initialize w/ custom panels and defaults
 *
 * @return void
 * @access public
 **/
	public function testInitializeCustomPanelsWithDefaults() {
		$this->_loadController(array(
			'panels' => array('test'),
		));

		$expected = array('history', 'session', 'request', 'sqlLog', 'timer', 'log', 'variables', 'test');
		$this->assertEqual($expected, array_keys($this->Controller->Toolbar->panels));
	}


/**
 * test syntax for removing panels
 *
 * @return void
 **/
	public function testInitializeRemovingPanels() {
		$this->_loadController(array(
			'panels' => array(
				'session' => false,
				'history' => false,
				'test'
			)
		));

		$expected = array('request', 'sqlLog', 'timer', 'log', 'variables', 'test');
		$this->assertEqual($expected, array_keys($this->Controller->Toolbar->panels));
	}

/**
 * ensure that Toolbar is not enabled when debug == 0 on initialize
 *
 * @return void
 **/
	public function testDebugDisableOnInitialize() {
		$_debug = Configure::read('debug');
		Configure::write('debug', 0);
		$this->_loadController();
		Configure::write('debug', $_debug);

		$this->assertFalse($this->Controller->Components->enabled('Toolbar'));
	}

/**
 * test that passing in forceEnable will enable the toolbar even if debug = 0
 *
 * @return void
 **/
	public function testForceEnable() {
		$_debug = Configure::read('debug');
		Configure::write('debug', 0);
		$this->_loadController(array(
			'forceEnable' => true,
		));
		Configure::write('debug', $_debug);

		$this->assertTrue($this->Controller->Components->enabled('Toolbar'));
	}

/**
 * Test disabling autoRunning of toolbar
 *
 * @return void
 **/
	public function testAutoRunSettingFalse() {
		$this->_loadController(array(
			'autoRun' => false,
		));
		$this->assertFalse($this->Controller->Components->enabled('Toolbar'));
	}

/**
 * test autorun = false with query string param
 *
 * @return void
 **/
	public function testAutoRunSettingWithQueryString() {
		$this->url = '/?debug=1';
		$_GET['debug'] = 1;
		$this->_loadController(array(
			'autoRun' => false,
		));
		$this->assertTrue($this->Controller->Components->enabled('Toolbar'));
	}

/**
 * test startup
 *
 * @return void
 **/
	public function testStartup() {
		$this->_loadController(array(
			'panels' => array('test'),
		));
		$MockPanel = $this->getMock('DebugPanel');
		$MockPanel->expects($this->once())->method('startup');
		$this->Controller->Toolbar->panels['test'] = $MockPanel;

		$this->Controller->Toolbar->startup($this->Controller);
		$this->assertEqual($this->Controller->viewClass, 'DebugKit.Debug');
		$this->assertTrue(isset($this->Controller->helpers['DebugKit.Toolbar']));

		$this->assertEqual($this->Controller->helpers['DebugKit.Toolbar']['output'], 'DebugKit.HtmlToolbar');
		$this->assertEqual($this->Controller->helpers['DebugKit.Toolbar']['cacheConfig'], 'debug_kit');
		$this->assertTrue(isset($this->Controller->helpers['DebugKit.Toolbar']['cacheKey']));

		$timers = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($timers['controllerAction']));
		$memory = DebugKitDebugger::getMemoryPoints();
		$this->assertTrue(isset($memory['Controller action start']));
	}

/**
 * Test that cache config generation works.
 *
 * @return void
 **/
	public function testCacheConfigGeneration() {
		$this->_loadController();
		$this->Controller->Components->trigger('startup', array($this->Controller));

		$results = Cache::config('debug_kit');
		$this->assertTrue(is_array($results));
	}

/**
 * test state saving of toolbar
 *
 * @return void
 **/
	public function testStateSaving() {
		$this->_loadController();
		$configName = 'debug_kit';
		$this->Controller->Toolbar->cacheKey = 'toolbar_history';

		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->set('test', 'testing');
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));

		$result = Cache::read('toolbar_history', $configName);
		$this->assertEqual($result[0]['variables']['content']['test'], 'testing');
		Cache::delete('toolbar_history', $configName);
	}

/**
 * Test Before Render callback
 *
 * @return void
 **/
	public function testBeforeRender() {
		$this->_loadController(array(
			'panels' => array('test', 'session'),
		));
		$MockPanel = $this->getMock('DebugPanel');
		$MockPanel->expects($this->once())->method('beforeRender');
		$this->Controller->Toolbar->panels['test'] = $MockPanel;
		$this->Controller->Toolbar->beforeRender($this->Controller);

		$this->assertTrue(isset($this->Controller->viewVars['debugToolbarPanels']));
		$vars = $this->Controller->viewVars['debugToolbarPanels'];

		$expected = array(
			'plugin' => 'debug_kit',
			'elementName' => 'session_panel',
			'content' => $this->Controller->Toolbar->Session->read(),
			'disableTimer' => true,
			'title' => ''
		);
		$this->assertEqual($expected, $vars['session']);

		$memory = DebugKitDebugger::getMemoryPoints();
		$this->assertTrue(isset($memory['Controller render start']));
	}

/**
 * test that vars are gathered and state is saved on beforeRedirect
 *
 * @return void
 **/
	public function testBeforeRedirect() {
		$this->_loadController(array(
			'panels' => array('test', 'session', 'history'),
		));

		$configName = 'debug_kit';
		$this->Controller->Toolbar->cacheKey = 'toolbar_history';
		Cache::delete('toolbar_history', $configName);

		DebugKitDebugger::startTimer('controllerAction', 'testing beforeRedirect');
		$MockPanel = $this->getMock('DebugPanel');
		$MockPanel->expects($this->once())->method('beforeRender');
		$this->Controller->Toolbar->panels['test'] = $MockPanel;
		$this->Controller->Toolbar->beforeRedirect($this->Controller);

		$result = Cache::read('toolbar_history', $configName);
		$this->assertTrue(isset($result[0]['session']));
		$this->assertTrue(isset($result[0]['test']));

		$timers = DebugKitDebugger::getTimers();
		$this->assertTrue(isset($timers['controllerAction']));
	}

/**
 * test that loading state (accessing cache) works.
 *
 * @return void
 **/
	public function testLoadState() {
		$this->_loadController();
		$this->Controller->Toolbar->cacheKey = 'toolbar_history';

		$data = array(0 => array('my data'));
		Cache::write('toolbar_history', $data, 'debug_kit');
		$result = $this->Controller->Toolbar->loadState(0);
		$this->assertEqual($result, $data[0]);
	}

/**
 * test the Log panel log reading.
 *
 * @return void
 **/
	public function testLogPanel() {
		$this->_loadController(array(
			'panels' => array(
				'log',
				'session',
				'history' => false,
				'variables' => false,
				'sqlLog' => false,
				'timer' => false,
			)
		));

		sleep(1);
		$this->Controller->log('This is a log I made this request');
		$this->Controller->log('This is the second  log I made this request');
		$this->Controller->log('This time in the debug log!', LOG_DEBUG);
		
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
		$result = $this->Controller->viewVars['debugToolbarPanels']['log'];

		$this->assertEqual(count($result['content']), 2);
		$this->assertEqual(count($result['content']['error']), 2);
		$this->assertEqual(count($result['content']['debug']), 1);

		$this->assertEqual(trim($result['content']['debug'][0][1]), 'This time in the debug log!');
		$this->assertEqual(trim($result['content']['error'][0][1]), 'This is a log I made this request');

		$data = array(
			'Post' => array(
				'id' => 1,
				'title' => 'post!',
				'body' => 'some text here',
				'created' => '2009-11-07 23:23:23'
			),
			'Comment' => array(
				'id' => 23
			)
		);
		$this->Controller->log($data);
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
		$result = $this->Controller->viewVars['debugToolbarPanels']['log'];
		$this->assertPattern('/\[created\] => 2009-11-07 23:23:23/', $result['content']['error'][2][1]);
		$this->assertPattern('/\[Comment\] => Array/', $result['content']['error'][2][1]);
	}


/**
 * test that creating the log panel creates the default file logger if none
 * are configured.  This stops DebugKit from mucking with the default auto-magic log config
 *
 * @return void
 */
	public function testLogPanelConstructCreatingDefaultLogConfiguration() {
		$this->_loadController();

		CakeLog::drop('default');
		CakeLog::drop('debug_kit_log_panel');

		$panel = new LogPanel(array());
		$configured = CakeLog::configured();

		$this->assertTrue(in_array('default', $configured));
		$this->assertTrue(in_array('debug_kit_log_panel', $configured));
	}


/**
 * Test that history state urls set prefix = null and admin = null so generated urls do not
 * adopt these params.
 *
 * @return void
 **/
	public function testHistoryUrlGenerationWithPrefixes() {
		$this->url = '/debugkit_url_with_prefixes_test';
		Router::connect($this->url, array(
			'controller' => 'posts',
			'action' => 'edit',
			'admin' => 1,
			'prefix' => 'admin',
			'plugin' => 'cms',
		));
		$this->_loadController();
		$this->Controller->Toolbar->cacheKey = 'url_test';
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));

		$result = $this->Controller->Toolbar->panels['history']->beforeRender($this->Controller);
		$expected = array(
			'plugin' => 'debug_kit',
			'controller' => 'toolbar_access',
			'action' => 'history_state',
			0 => 1,
			'admin' => false,
		);
		$this->assertEqual($result[0]['url'], $expected);
		Cache::delete('url_test', 'debug_kit');
	}

/**
 * Test that the FireCake toolbar is used on AJAX requests
 *
 * @return void
 **/
	public function testAjaxToolbar() {
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->_loadController();
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->assertEqual($this->Controller->helpers['DebugKit.Toolbar']['output'], 'DebugKit.FirePhpToolbar');
	}

/**
 * Test that the toolbar does not interfere with requestAction
 *
 * @return void
 **/
	public function testNoRequestActionInterference() {
		$debugKitPath = App::pluginPath('DebugKit');
		$noDir = (empty($debugKitPath) || !file_exists($debugKitPath));
		if ($noDir) {
			$this->markTestAsSkipped('Could not find debug_kit in plugin paths');
		}

		App::build(array(
			'controllers' => $debugKitPath . 'tests' . DS . 'test_app' . DS . 'controllers' . DS,
			'views' => array(
				$debugKitPath . 'tests' . DS . 'test_app' . DS . 'views' . DS,
				CAKE_CORE_INCLUDE_PATH . DS . 'cake' . DS . 'libs' . DS . 'view' . DS
			),
			'plugins' => $this->_paths['plugins']
		));
		Router::reload();
		$this->_loadController();

		$result = $this->Controller->requestAction('/debug_kit_test/request_action_return', array('return'));
		$this->assertEqual($result, 'I am some value from requestAction.');

		$result = $this->Controller->requestAction('/debug_kit_test/request_action_render', array('return'));
		$this->assertEqual($result, 'I have been rendered.');
	}

/**
 * test the sqlLog panel parsing of db->showLog
 *
 * @return void
 **/
	public function testSqlLogPanel() {
		App::import('Core', 'Model');
		$Article = ClassRegistry::init('Article');
		$Article->find('first', array('conditions' => array('Article.id' => 1)));

		$this->_loadController(array(
			'panels' => array('SqlLog'),
		));
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
		$result = $this->Controller->viewVars['debugToolbarPanels']['sql_log'];

		$this->assertTrue(isset($result['content']['connections'][$Article->useDbConfig]));
		$this->assertTrue(isset($result['content']['threshold']));
	}
}

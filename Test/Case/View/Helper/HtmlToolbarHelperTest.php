<?php
/**
 * Toolbar HTML Helper Test Case
 *
 * PHP versions 5
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
App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('Router', 'Routing');
App::uses('CakeResponse', 'Network');
App::uses('ToolbarHelper', 'DebugKit.View/Helper');
App::uses('HtmlToolbarHelper', 'DebugKit.View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

class HtmlToolbarHelperTestCase extends CakeTestCase {

	public static function setupBeforeClass() {
		App::build(array(
			'View' => array(
				CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'Test' . DS . 'test_app' . DS . 'View' . DS,
				APP . 'Plugin' . DS . 'DebugKit' . DS . 'View' . DS,
				CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'View' . DS
			)
		), true);
	}

	public static function tearDownAfterClass() {
		App::build();
	}

/**
 * setUp
 *
 * @return void
 **/
	public function setUp() {
		parent::setUp();

		Router::connect('/:controller/:action');

		$request = new CakeRequest();
		$request->addParams(array('controller' => 'pages', 'action' => 'display'));

		$this->Controller = new Controller($request, new CakeResponse());
		$this->View = new View($this->Controller);
		$this->Toolbar = new ToolbarHelper($this->View, array('output' => 'DebugKit.HtmlToolbar'));
		$this->Toolbar->HtmlToolbar = new HtmlToolbarHelper($this->View);
		$this->Toolbar->HtmlToolbar->Html = new HtmlHelper($this->View);
		$this->Toolbar->HtmlToolbar->Form = new FormHelper($this->View);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Toolbar, $this->Controller);
		ClassRegistry::flush();
	}

/**
 * test Neat Array formatting
 *
 * @return void
 **/
	public function testMakeNeatArray() {
		$in = false;
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', '0' , '/strong', '(false)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = null;
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', '0' , '/strong', '(null)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = true;
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', '0' , '/strong', '(true)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array();
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', '0' , '/strong', '(empty)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array('key' => 'value');
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array('key' => null);
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', 'key', '/strong', '(null)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array('key' => 'value', 'foo' => 'bar');
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'<li', '<strong', 'foo', '/strong', 'bar', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array(
			'key' => 'value',
			'foo' => array(
				'this' => 'deep',
				'another' => 'value'
			)
		);
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'<li', '<strong', 'foo', '/strong',
				array('ul' => array('class' => 'neat-array depth-1')),
				'<li', '<strong', 'this', '/strong', 'deep', '/li',
				'<li', '<strong', 'another', '/strong', 'value', '/li',
				'/ul',
			'/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array(
			'key' => 'value',
			'foo' => array(
				'this' => 'deep',
				'another' => 'value'
			),
			'lotr' => array(
				'gandalf' => 'wizard',
				'bilbo' => 'hobbit'
			)
		);
		$result = $this->Toolbar->makeNeatArray($in, 1);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0 expanded'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'<li', '<strong', 'foo', '/strong',
				array('ul' => array('class' => 'neat-array depth-1')),
				'<li', '<strong', 'this', '/strong', 'deep', '/li',
				'<li', '<strong', 'another', '/strong', 'value', '/li',
				'/ul',
			'/li',
			'<li', '<strong', 'lotr', '/strong',
				array('ul' => array('class' => 'neat-array depth-1')),
				'<li', '<strong', 'gandalf', '/strong', 'wizard', '/li',
				'<li', '<strong', 'bilbo', '/strong', 'hobbit', '/li',
				'/ul',
			'/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$result = $this->Toolbar->makeNeatArray($in, 2);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0 expanded'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'<li', '<strong', 'foo', '/strong',
				array('ul' => array('class' => 'neat-array depth-1 expanded')),
				'<li', '<strong', 'this', '/strong', 'deep', '/li',
				'<li', '<strong', 'another', '/strong', 'value', '/li',
				'/ul',
			'/li',
			'<li', '<strong', 'lotr', '/strong',
				array('ul' => array('class' => 'neat-array depth-1 expanded')),
				'<li', '<strong', 'gandalf', '/strong', 'wizard', '/li',
				'<li', '<strong', 'bilbo', '/strong', 'hobbit', '/li',
				'/ul',
			'/li',
			'/ul'
		);
		$this->assertTags($result, $expected);

		$in = array('key' => 'value', 'array' => array());
		$result = $this->Toolbar->makeNeatArray($in);
		$expected = array(
			'ul' => array('class' => 'neat-array depth-0'),
			'<li', '<strong', 'key', '/strong', 'value', '/li',
			'<li', '<strong', 'array', '/strong', '(empty)', '/li',
			'/ul'
		);
		$this->assertTags($result, $expected);
	}

/**
 * Test injection of toolbar
 *
 * @return void
 **/
	public function testInjectToolbar() {
		$this->Controller->viewPath = 'Posts';
		$request = new CakeRequest('/posts/index');
		$request->addParams(Router::parse($request->url));
		$request->addPaths(array(
			'webroot' => '/',
			'base' => '/',
			'here' => '/posts/index',
		));
		$this->Controller->setRequest($request);
		$this->Controller->helpers = array('Html', 'Js', 'Session', 'DebugKit.Toolbar');
		$this->Controller->layout = 'default';
		$this->Controller->uses = null;
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->constructClasses();
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
		$result = $this->Controller->render();
		$result = str_replace(array("\n", "\r"), '', $result);
		$this->assertPattern('#<div id\="debug-kit-toolbar">.+</div>.*</body>#', $result);
	}

/**
 * test injection of javascript
 *
 * @return void
 **/
	public function testJavascriptInjection() {
		$this->Controller->viewPath = 'Posts';
		$this->Controller->uses = null;
		$request = new CakeRequest('/posts/index');
		$request->addParams(Router::parse($request->url));
		$request->addPaths(array(
			'webroot' => '/',
			'base' => '/',
			'here' => '/posts/index',
		));
		$this->Controller->setRequest($request);
		$this->Controller->helpers = array('Js', 'Html', 'Session');
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->layout = 'default';
		$this->Controller->constructClasses();
		$this->Controller->Components->trigger('startup', array($this->Controller));
		$this->Controller->Components->trigger('beforeRender', array($this->Controller));
		$result = $this->Controller->render();
		$result = str_replace(array("\n", "\r"), '', $result);
		$this->assertPattern('#<script\s*type="text/javascript"\s*src="/debug_kit/js/js_debug_toolbar.js(?:\?\d*?)?"\s*>\s?</script>#', $result);
	}

/**
 * test message creation
 *
 * @return void
 */
	public function testMessage() {
		$result = $this->Toolbar->message('test', 'one, two');
		$expected = array(
			'<p',
				'<strong', 'test', '/strong',
				' one, two',
			'/p',
		);
		$this->assertTags($result, $expected);
	}
/**
 * Test Table generation
 *
 * @return void
 */
	public function testTable() {
		$rows = array(
			array(1,2),
			array(3,4),
		);
		$result = $this->Toolbar->table($rows);
		$expected = array(
			'table' => array('class' => 'debug-table'),
			array('tr' => array('class' => 'odd')),
			'<td', '1', '/td',
			'<td', '2', '/td',
			'/tr',
			array('tr' => array('class' => 'even')),
			'<td', '3', '/td',
			'<td', '4', '/td',
			'/tr',
			'/table'
		);
		$this->assertTags($result, $expected);
	}
/**
 * test starting a panel
 *
 * @return void
 **/
	public function testStartPanel() {
		$result = $this->Toolbar->panelStart('My Panel', 'my_panel');
		$expected = array(
			'a' => array('href' => '#my_panel'),
			'My Panel',
			'/a'
		);
		$this->assertTags($result, $expected);
	}
/**
 * test ending a panel
 *
 * @return void
 **/
	public function testPanelEnd() {
		$result = $this->Toolbar->panelEnd();
		$this->assertNull($result);
	}
}

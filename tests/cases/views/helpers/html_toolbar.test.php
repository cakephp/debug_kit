<?php
/**
 * Toolbar HTML Helper Test Case
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
App::import('Helper', array('DebugKit.HtmlToolbar', 'Html', 'Form'));
App::import('Core', array('View', 'Controller'));

class HtmlToolbarHelperTestCase extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 **/
	function setUp() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		
		$this->Toolbar =& new ToolbarHelper(array('output' => 'DebugKit.HtmlToolbar'));
		$this->Toolbar->HtmlToolbar =& new HtmlToolbarHelper();
		$this->Toolbar->HtmlToolbar->Html =& new HtmlHelper();
		$this->Toolbar->HtmlToolbar->Form =& new FormHelper();
		
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
		$this->_viewPaths = App::path('views');
		App::build(array(
			'views' => array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		)), true);
		$this->_debug = Configure::read('debug');
	}
/**
 * test Neat Array formatting
 *
 * @return void
 **/
	function testMakeNeatArray() {
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
		$this->Controller->helpers = array('Html', 'Javascript', 'Session', 'DebugKit.Toolbar');
		$this->Controller->layout = 'default';
		$this->Controller->uses = null;
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$result = $this->Controller->render();
		$result = str_replace(array("\n", "\r"), '', $result);
		$this->assertPattern('#<div id\="debug-kit-toolbar">.+</div>.*</body>#', $result);
	}

/**
 * test injection of javascript
 *
 * @return void
 **/
	function testJavascriptInjection() {
		$this->Controller->viewPath = 'posts';
		$this->Controller->uses = null;
		$this->Controller->action = 'index';
		$this->Controller->params = array(
			'action' => 'index',
			'controller' => 'posts',
			'plugin' => null,
			'url' => array('url' => 'posts/index'),
			'base' => '/',
			'here' => '/posts/index',
		);
		$this->Controller->helpers = array('Javascript', 'Html', 'Session');
		$this->Controller->components = array('DebugKit.Toolbar');
		$this->Controller->layout = 'default';
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->Component->startup($this->Controller);
		$this->Controller->Component->beforeRender($this->Controller);
		$result = $this->Controller->render();
		$result = str_replace(array("\n", "\r"), '', $result);
		$this->assertPattern('#<script\s*type="text/javascript"\s*src="/debug_kit/js/js_debug_toolbar.js(:?\?\d*?)?"\s*>\s?</script>#', $result);
	}

/**
 * test message creation
 *
 * @return void
 */
	function testMessage() {
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
	function testTable() {
		$rows = array(
			array(1,2),
			array(3,4),
		);
		$result = $this->Toolbar->table($rows);
		$expected = array(
			'table' => array('class' =>'debug-table'),
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
	function testStartPanel() {
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
	function testPanelEnd() {
		$result = $this->Toolbar->panelEnd();
		$this->assertNull($result);
	}
/**
 * reset the view paths
 *
 * @return void
 **/
	function endCase() {
		App::build();
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
	}
}

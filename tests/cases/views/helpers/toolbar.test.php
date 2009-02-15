<?php
/* SVN FILE: $Id$ */
/**
 * Toolbar facade tests.
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
 * @subpackage		debug_kit.tests.views.helpers
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Helper', array('DebugKit.Toolbar'));
App::import('Core', array('View', 'Controller'));

Mock::generate('Helper', 'MockBackendHelper', array('testMethod'));

class ToolbarHelperTestCase extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 **/
	function setUp() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		
		$this->Toolbar =& new ToolbarHelper(array(
			'output' => 'MockBackendHelper',
			'cacheKey' => 'debug_kit_toolbar_test_case',
			'cacheConfig' => 'default'
		));
		$this->Toolbar->MockBackend = new MockBackendHelper();
		
		$this->Controller =& ClassRegistry::init('Controller');
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
		$this->_viewPaths = Configure::read('viewPaths');
		Configure::write('viewPaths', array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		));
		$this->_debug = Configure::read('debug');
	}
/**
 * test cache writing for views.
 *
 * @return void
 **/
	function testCacheWrite() {
		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertTrue($result);
	}
/**
 * Ensure that the cache writing only affects the 
 * top most level of the history stack. As this is where the current request is stored.
 *
 * @return void
 **/
	function testOnlyWritingToFirstElement() {
		$values = array(
			array('test' => array('content' => array('first', 'values'))),
			array('test' => array('content' => array('second', 'values'))),
		);
		Cache::write('debug_kit_toolbar_test_case', $values);
		$this->Toolbar->writeCache('test', array('new', 'values'));
		
		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('new', 'values'));
		
		$result = $this->Toolbar->readCache('test', 1);
		$this->assertEqual($result, array('second', 'values'));
	}
/**
 * test cache reading for views
 *
 * @return void
 **/
	function testCacheRead() {
		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertTrue($result, 'Cache write failed %s');
		
		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('stuff', 'to', 'cache'), 'Cache value is wrong %s');
		
		$result = $this->Toolbar->writeCache('test', array('new', 'stuff'));
		$this->assertTrue($result, 'Cache write failed %s');
		
		$result = $this->Toolbar->readCache('test');
		$this->assertEqual($result, array('new', 'stuff'), 'Cache value is wrong %s');
	}
/**
 * Test that reading/writing doesn't work with no cache config.
 *
 * @return void
 **/
	function testNoCacheConfigPresent() {
		$this->Toolbar = new ToolbarHelper(array('output' => 'MockBackendHelper'));
		
		$result = $this->Toolbar->writeCache('test', array('stuff', 'to', 'cache'));
		$this->assertFalse($result, 'Writing to cache succeeded with no cache config %s');
			
		$result = $this->Toolbar->readCache('test');
		$this->assertFalse($result, 'Reading cache succeeded with no cache config %s');
	}
/**
 * reset the view paths
 *
 * @return void
 **/
	function endCase() {
		Configure::write('viewPaths', $this->_viewPaths);
		Cache::delete('debug_kit_toolbar_test_case', 'default');
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
?>
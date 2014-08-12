<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Model;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Model\ToolbarAccess;

/**
 * Test case for ToolbarAccess model
 *
 */
class ToolbarAccessTestCase extends TestCase {

/**
 * Included fixtures
 *
 * @var array
 */
	public $fixtures = array('core.post');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Model = new ToolbarAccess();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Model);
	}

/**
 * test that explain query returns arrays of query information.
 *
 * @return void
 */
	public function testExplainQuery() {
		$this->markTestIncomplete('Test only works on MySQL or Postgres');
		$posts = TableRegistry::get('Posts');
		$sql = 'SELECT * FROM posts';
		$result = $this->Model->explainQuery('test', $sql);

		$this->assertTrue(is_array($result));
		$this->assertFalse(empty($result));
	}
}

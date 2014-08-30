<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 2.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Panel;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\SqlLogPanel;

/**
 * Class SqlLogPanelTest
 */
class SqlLogPanelTest extends TestCase {

/**
 * fixtures.
 *
 * @var array
 */
	public $fixtures = ['core.article'];

/**
 * Setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->panel = new SqlLogPanel();
	}

/**
 * test the parsing of source list.
 *
 * @return void
 */
	public function testShutdown() {
		$event = new Event('Sample');
		$result = $this->panel->initialize($event);

		$articles = TableRegistry::get('Articles');
		$articles->findById(1)->first();

		$this->panel->shutdown($event);
		$result = $this->panel->data();

		$this->assertArrayHasKey('loggers', $result);
		$this->assertCount(3, $result['loggers']);
	}
}

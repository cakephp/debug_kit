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
 * @since         DebugKit 3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Test\TestCase\Controller;

use Cake\Routing\Router;
use Cake\TestSuite\ControllerTestCase;

/**
 * Panel controller test.
 */
class PanelsControllerTestCase extends ControllerTestCase {

/**
 * Fixtures.
 *
 * @var array
 */
	public $fixtures = [
		'plugin.debug_kit.request',
		'plugin.debug_kit.panel'
	];

/**
 * Don't reload routes.
 *
 * @var bool
 */
	public $loadRoutes = false;

/**
 * Setup method.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Router::plugin('DebugKit', function($routes) {
			$routes->connect('/panels/:action/*', ['controller' => 'Panels']);
		});
	}

/**
 * Test getting a panel that exists.
 *
 * @return void
 */
	public function testView() {
		$result = $this->testAction('/debug_kit/panels/view/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', ['return' => 'contents']);
		$this->assertContains('Request</h2>', $result);
		$this->assertContains('Routing Params</h4>', $result);
	}

/**
 * Test getting a panel that does notexists.
 *
 * @expectedException Cake\ORM\Exception\RecordNotFoundException
 * @return void
 */
	public function testViewNotExists() {
		$this->testAction('/debug_kit/panels/view/aaaaaaaa-ffff-ffff-ffff-aaaaaaaaaaaa', ['return' => 'contents']);
	}

}

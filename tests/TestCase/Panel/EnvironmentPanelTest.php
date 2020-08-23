<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\EnvironmentPanel;

/**
 * Class EnvironmentPanelTest
 */
class EnvironmentPanelTest extends TestCase
{
    /**
     * @var EnvironmentPanel
     */
    protected $panel;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->panel = new EnvironmentPanel();
    }

    /**
     * Teardown method.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->panel);
    }

    /**
     * test shutdown
     *
     * @return void
     */
    public function testShutdown()
    {
        $controller = new \stdClass();
        $event = new Event('Controller.shutdown', $controller);
        $_SERVER['TEST_URL_1'] = 'mysql://user:password@localhost/my_db';

        $this->panel->shutdown($event);
        $output = $this->panel->data();
        $this->assertIsArray($output);
        $this->assertSame(['php', 'ini', 'cake', 'app'], array_keys($output));
        $this->assertSame('mysql://user:password@localhost/my_db', $output['php']['TEST_URL_1']);
    }
}

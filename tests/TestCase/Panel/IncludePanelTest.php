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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\IncludePanel;

/**
 * Class IncludePanelTest
 *
 */
class IncludePanelTest extends TestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->panel = new IncludePanel();
    }

    /**
     * test shutdown
     *
     * @return void
     */
    public function testShutdown()
    {
        $result = $this->panel->shutdown(new Event('Controller.shutdown'));
        $this->assertNull($result);

        $data = $this->panel->data();
        $this->assertArrayHasKey('core', $data);
        $this->assertArrayHasKey('app', $data);
        $this->assertArrayHasKey('plugins', $data);
        $this->assertArrayHasKey('DebugKit', $data['plugins']);
        $this->assertArrayHasKey('Other', $data['plugins']['DebugKit']);
    }

    /**
     * Test that the log panel outputs a summary.
     *
     * @return void
     */
    public function testSummary()
    {
        $total = $this->panel->summary();
        $this->assertGreaterThan(50, $total);
    }
}

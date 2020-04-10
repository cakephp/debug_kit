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

use Cake\Log\Log;
use Cake\TestSuite\TestCase;
use DebugKit\Log\Engine\DebugKitLog;
use DebugKit\Panel\LogPanel;

/**
 * Class LogPanelTest
 */
class LogPanelTest extends TestCase
{
    /**
     * @var LogPanel
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
        $this->panel = new LogPanel();
    }

    /**
     * Teardown method.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Log::drop('debug_kit_log_panel');
    }

    /**
     * Test that logging configs are created.
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->panel->initialize();

        $result = Log::configured();
        $this->assertContains('debug_kit_log_panel', $result);
        $this->assertTrue(count($result) > 1, 'Default loggers were not added.');
    }

    /**
     * test shutdown
     *
     * @return void
     */
    public function testData()
    {
        $this->panel->initialize();
        Log::write('error', 'Test');

        $result = $this->panel->data();

        $this->assertArrayHasKey('logger', $result);
        /** @var \DebugKit\Log\Engine\DebugKitLog $logger */
        $logger = $result['logger'];

        $this->assertInstanceOf(DebugKitLog::class, $logger);
        $this->assertCount(1, $logger->all()['error']);
    }

    /**
     * Test that the log panel outputs a summary.
     *
     * @return void
     */
    public function testSummary()
    {
        $this->panel->initialize();

        Log::write('error', 'Test');
        $this->assertSame('1', $this->panel->summary());

        Log::write('error', 'Test 2');
        Log::write('notice', 'A thing');
        $this->assertSame('3', $this->panel->summary());
    }
}

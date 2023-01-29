<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 **/
namespace DebugKit\Test\TestCase\Panel;

use Cake\Event\Event;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\IncludePanel;

/**
 * Class IncludePanelTest
 */
class IncludePanelTest extends TestCase
{
    /**
     * @var IncludePanel
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
        $this->panel = new IncludePanel();
    }

    /**
     * test shutdown
     *
     * @return void
     */
    public function testShutdown()
    {
        $this->panel->shutdown(new Event('Controller.shutdown'));

        $data = $this->panel->data();
        $this->assertArrayHasKey('cake', $data);
        $this->assertArrayHasKey('app', $data);

        $this->assertArrayHasKey('plugins', $data);
        $this->assertArrayHasKey('DebugKit', $data['plugins']);
        $this->assertArrayHasKey('other', $data['plugins']['DebugKit']);

        $this->assertArrayHasKey('vendor', $data);

        $this->assertArrayHasKey('other', $data);
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

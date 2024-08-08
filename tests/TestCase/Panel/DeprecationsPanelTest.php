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
use DebugKit\Panel\DeprecationsPanel;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use function Cake\Core\deprecationWarning;

/**
 * Class DeprecationsPanelTest
 */
class DeprecationsPanelTest extends TestCase
{
    /**
     * @var DeprecationsPanel
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
        DeprecationsPanel::clearDeprecatedErrors();

        $this->loadPlugins(['DebugKit']);
        $this->panel = new DeprecationsPanel();

        set_error_handler(function ($code, $message, $file, $line, $context = null) {
            DeprecationsPanel::addDeprecatedError(compact('code', 'message', 'file', 'line', 'context'));
        });
        try {
            deprecationWarning('x.x', 'Something going away', 0);
            deprecationWarning('x.x', 'Something else going away', 0);
            trigger_error('Raw error', E_USER_DEPRECATED);
        } finally {
            restore_error_handler();
        }
    }

    #[WithoutErrorHandler]
    public function testShutdown()
    {
        $event = new Event('Panel.shutdown');
        $this->panel->shutdown($event);
        $data = $this->panel->data();

        $this->assertArrayHasKey('app', $data);
        $this->assertArrayHasKey('cake', $data);
        $this->assertArrayHasKey('vendor', $data);
        $this->assertArrayHasKey('plugins', $data);
        $this->assertArrayHasKey('other', $data);
        $this->assertCount(3, $data['plugins']['DebugKit']);

        $error = $data['plugins']['DebugKit'][0];
        $this->assertStringContainsString('Something going away', $error['message']);
        $this->assertArrayHasKey('niceFile', $error);
        $this->assertArrayHasKey('line', $error);
    }

    public function testSummary()
    {
        $this->assertSame('1', $this->panel->summary());
    }
}

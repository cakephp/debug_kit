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
 */
namespace DebugKit\Test\TestCase;

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\DeprecationsPanel;
use DebugKit\Plugin;
use DebugKit\ToolbarService;

/**
 * Test the Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Test setDeprecationHandler.
     *
     * @return void
     */
    public function testSetDeprecationHandler()
    {
        DeprecationsPanel::clearDeprecatedErrors();
        $service = new ToolbarService(new EventManager(), []);
        $plugin = new Plugin();
        $plugin->setDeprecationHandler($service);
        $event = new Event('');
        $panel = new DeprecationsPanel();

        //Without setting the $stackFrame
        deprecationWarning('setDeprecationHandler');
        //Setting the $stackFrame
        deprecationWarning('setDeprecationHandler_2', 2);
        //Raw error
        $line = __LINE__ + 1;
        trigger_error('raw_error', E_USER_DEPRECATED);

        $panel->shutdown($event);
        $data = $panel->data()['plugins']['DebugKit'];

        $this->assertCount(3, $data);

        //test first two deprecationWarning()
        foreach ([$data[0], $data[1]] as $value) {
            $this->assertStringContainsString($value['file'], $value['message']);
            $this->assertStringContainsString("line: {$value['line']}", $value['message']);
        }
        //test raw error
        $this->assertSame($line, $data[2]['line']);
    }
}

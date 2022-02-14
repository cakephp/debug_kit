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

use Cake\Error\PhpError;
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
        $panel = new DeprecationsPanel();

        $error = new PhpError(E_USER_WARNING, 'ignored', __FILE__, __LINE__, []);
        $event = new Event('Error.handled', null, ['error' => $error]);
        EventManager::instance()->dispatch($event);

        // No file/line in message.
        $error = new PhpError(E_USER_DEPRECATED, 'going away', __FILE__, __LINE__, []);
        $event = new Event('Error.handled', null, ['error' => $error]);
        EventManager::instance()->dispatch($event);

        // Formatted like deprecationWarning()
        $message = <<<TEXT
Something deprecated happened.
Don't use that thing.
src/Plugin.php, line: 51
You can disable all deprecation warnings by setting `Error.errorLevel` to `E_ALL & ~E_USER_DEPRECATED`.
TEXT;
        $error = new PhpError(E_USER_DEPRECATED, $message, __FILE__, __LINE__, []);
        $event = new Event('Error.handled', null, ['error' => $error]);
        EventManager::instance()->dispatch($event);

        $panel->shutdown($event);
        $data = $panel->data();

        $this->assertArrayHasKey('plugins', $data);
        $this->assertArrayHasKey('DebugKit', $data['plugins']);
        $this->assertCount(1, $data['plugins']['DebugKit']);

        $first = $data['plugins']['DebugKit'][0];
        $this->assertEquals($first['message'], 'going away');
        $this->assertEquals($first['file'], __FILE__);
        $this->assertEquals($first['line'], 48);

        $this->assertArrayHasKey('other', $data);
        $parsed = $data['other'][0];
        $this->assertEquals($parsed['message'], $message);
        $this->assertEquals($parsed['file'], 'src/Plugin.php');
        $this->assertEquals($parsed['line'], 51);
    }
}

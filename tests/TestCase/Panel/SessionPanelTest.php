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

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use DebugKit\Panel\SessionPanel;

/**
 * Class RequestPanelTest
 */
class SessionPanelTest extends TestCase
{
    /**
     * @var SessionPanel
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
        $this->panel = new SessionPanel();
    }

    /**
     * Test that shutdown will skip unserializable attributes.
     *
     * @return void
     */
    public function testShutdownSkipAttributes()
    {
        $session = new Session();
        $session->write('test', 123);
        $request = new ServerRequest([
            'session' => $session,
        ]);

        $controller = new Controller($request);
        $event = new Event('Controller.shutdown', $controller);
        $this->panel->shutdown($event);

        $data = $this->panel->data();
        $this->assertArrayHasKey('content', $data);
        /** @var \Cake\Error\Debug\ArrayItemNode $content */
        $content = $data['content']->getChildren()[0];
        $this->assertEquals('test', $content->getKey()->getValue());
        $this->assertEquals('123', $content->getValue()->getValue());
    }
}

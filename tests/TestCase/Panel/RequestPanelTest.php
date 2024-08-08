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
use Cake\TestSuite\TestCase;
use DebugKit\Panel\RequestPanel;

/**
 * Class RequestPanelTest
 */
class RequestPanelTest extends TestCase
{
    /**
     * @var RequestPanel
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
        $this->panel = new RequestPanel();
    }

    /**
     * Test that shutdown will skip unserializable attributes.
     *
     * @return void
     */
    public function testShutdownSkipAttributes()
    {
        $request = new ServerRequest([
            'url' => '/',
            'post' => ['name' => 'bob'],
            'query' => ['page' => 1],
        ]);
        $request = $request
            ->withAttribute('ok', 'string')
            ->withAttribute('closure', function () {
            });

        $controller = new Controller($request);
        $event = new Event('Controller.shutdown', $controller);
        $this->panel->shutdown($event);

        $data = $this->panel->data();
        $this->assertArrayHasKey('attributes', $data);
        $this->assertArrayHasKey('session', $data);
        $this->assertArrayHasKey('params', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('cookie', $data);
        $this->assertEquals('string', $data['attributes']['ok']->getType());
        $this->assertStringContainsString('Could not serialize `closure`', $data['attributes']['closure']->getValue());
    }
}

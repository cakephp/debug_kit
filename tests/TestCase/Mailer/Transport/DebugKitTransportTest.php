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
 */
namespace DebugKit\Test\TestCase\Mailer\Transport;

use ArrayObject;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Message;
use Cake\TestSuite\TestCase;
use DebugKit\Mailer\Transport\DebugKitTransport;

class DebugKitTransportTest extends TestCase
{
    protected ArrayObject $log;

    protected DebugKitTransport $transport;

    protected $wrapped;

    public function setUp(): void
    {
        $this->log = new ArrayObject();
        $this->wrapped = new class extends AbstractTransport {
            public string $property;

            public function send(Message $message): array
            {
                return [];
            }

            public function customMethod(): string
            {
                return 'bloop';
            }
        };
        $this->transport = new DebugKitTransport(
            ['debugKitLog' => $this->log],
            $this->wrapped,
        );
    }

    public function testPropertyProxies()
    {
        $this->wrapped->property = 'value';
        $this->assertTrue(isset($this->transport->property));
        $this->assertSame('value', $this->transport->property);

        $this->transport->property = 'new value';
        $this->assertSame('new value', $this->wrapped->property);
        unset($this->transport->property);
        $this->assertFalse(isset($this->wrapped->property));
    }

    public function testMethodProxy()
    {
        $this->assertSame('bloop', $this->transport->customMethod());
    }

    public function testEmailCapture()
    {
        $message = new Message();
        $message->setSubject('Testing 123')
            ->setFrom('sender@example.com')
            ->setTo('to@example.com');
        $this->transport->send($message);
        $this->assertCount(1, $this->log);

        $result = $this->log[0];
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('message', $result);
    }
}

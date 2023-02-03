<?php
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

use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use DebugKit\Mailer\Transport\DebugKitTransport;

class DebugKitTransportTest extends TestCase
{
    public function setUp()
    {
        $this->log = new \ArrayObject();
        $this->wrapped = $this->getMockBuilder(AbstractTransport::class)
            ->setMethods(['send', 'customMethod'])
            ->getMock();
        $this->transport = new DebugKitTransport(
            ['debugKitLog' => $this->log],
            $this->wrapped
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
        $this->wrapped->method('customMethod')
            ->will($this->returnValue('bloop'));
        $this->assertSame('bloop', $this->transport->customMethod());
    }

    public function testEmailCapture()
    {
        $email = new Email();
        $email->setSubject('Testing 123')
            ->setFrom('sender@example.com')
            ->setTo('to@example.com');
        $this->transport->send($email);
        $this->assertCount(1, $this->log);

        $result = $this->log[0];
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('message', $result);
    }
}

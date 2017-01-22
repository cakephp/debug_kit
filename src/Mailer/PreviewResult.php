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
 * @since         3.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Mailer;

use Cake\Mailer\Mailer;
use ReflectionClass;

class PreviewResult
{

    protected $mailer;

    protected $method;

    protected $reflection;

    protected $headers = [];

    protected $parts = [];

    public function __construct(Mailer $mailer, $method)
    {
        $this->mailer = clone $mailer;
        $this->method = $method;
        $this->reflection = new ReflectionClass($this->mailer);
        $this->processMailer();
        $mailer->reset();
    }

    protected function processMailer()
    {
        $mailer = $this->mailer;

        if (!$mailer->template()) {
            $mailer->template($this->method);
        }

        $prop = $this->reflection->getProperty('_email');
        $prop->setAccessible(true);
        $email = $prop->getValue($mailer);

        $render = (new ReflectionClass($email))
            ->getMethod('_renderTemplates')
            ->getClosure($email);

        $this->parts = $render('');

        $extra = ['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'subject'];
        $this->headers = array_filter($email->getHeaders($extra));
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getParts()
    {
        return $this->parts;
    }
}

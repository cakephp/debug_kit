<?php
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

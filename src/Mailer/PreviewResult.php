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
namespace DebugKit\Mailer;

use Cake\Mailer\Mailer;
use Cake\Mailer\Renderer;
use ReflectionClass;

/**
 * Represents the result of a preview for a given mailer
 */
class PreviewResult extends AbstractResult
{
    /**
     * Processes the mailer to extract the headers and parts
     *
     * @param \Cake\Mailer\Mailer $mailer The mailer instance to execute and extract the email data from
     * @param string $method The method to execute in the mailer
     */
    public function __construct(Mailer $mailer, $method)
    {
        $this->processMailer(clone $mailer, $method);
        $mailer->reset();
    }

    /**
     * Executes the mailer and extracts the relevant information from the generated email
     *
     * @param \Cake\Mailer\Mailer $mailer The mailer instance to execute and extract the email data from
     * @param string $method The method to execute in the mailer
     * @return void
     */
    protected function processMailer(Mailer $mailer, $method)
    {
        if (!$mailer->viewBuilder()->getTemplate()) {
            $mailer->viewBuilder()->setTemplate($method);
        }

        $reflection = new ReflectionClass($mailer);
        $prop = $reflection->getProperty('_email');
        $prop->setAccessible(true);
        $email = $prop->getValue($mailer);

        $reflection = new ReflectionClass($email);
        $prop = $reflection->getProperty('renderer');
        $prop->setAccessible(true);
        $renderer = $prop->getValue($email);

        $reflection = new ReflectionClass($renderer);
        $prop = $reflection->getProperty('email');
        $prop->setAccessible(true);
        $prop->setValue($renderer, $email);

        $method = $reflection->getMethod('renderTemplates');
        $closure = $method->getClosure($renderer);

        $this->parts = $closure('');

        $extra = ['from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'subject'];
        $this->headers = array_filter($email->getHeaders($extra));
    }
}

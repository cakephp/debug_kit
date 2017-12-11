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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Panel;

use ArrayObject;
use Cake\Core\App;
use Cake\Mailer\Email;
use DebugKit\DebugPanel;
use DebugKit\Mailer\Transport\DebugKitTransport;
use ReflectionClass;

/**
 * Provides debug information on the Emails sent during the request
 */
class MailPanel extends DebugPanel
{

    /**
     * The list of emails produced during the request
     *
     * @var ArrayObject
     */
    protected $emailLog;

    /**
     * Initialize hook - configures the email transport.
     *
     * @return void
     */
    public function initialize()
    {
        $reflection = new ReflectionClass(Email::class);
        $property = $reflection->getProperty('_transportConfig');
        $property->setAccessible(true);
        $configs = $property->getValue();

        $log = $this->emailLog = new ArrayObject;

        foreach ($configs as $name => &$transport) {
            if (is_object($transport)) {
                $configs[$name] = new DebugKitTransport(['debugKitLog' => $log], $transport);
                continue;
            }

            $className = App::className($transport['className'], 'Mailer/Transport', 'Transport');

            if (!$className) {
                continue;
            }

            $transport['originalClassName'] = $transport['className'];
            $transport['className'] = 'DebugKit.DebugKit';
            $transport['debugKitLog'] = $log;
        }
        $property->setValue($configs);
    }

    /**
     * Get the data this panel wants to store.
     *
     * @return array
     */
    public function data()
    {
        return [
            'emails' => isset($this->emailLog) ? $this->emailLog->getArrayCopy() : []
        ];
    }

    /**
     * Get summary data from the queries run.
     *
     * @return string
     */
    public function summary()
    {
        if (empty($this->emailLog)) {
            return '';
        }

        return (string)count($this->emailLog);
    }
}

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

use Cake\Datasource\ModelAwareTrait;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Base class for Mailer Previews.
 */
class MailPreview
{
    use LocatorAwareTrait;
    use MailerAwareTrait;
    use ModelAwareTrait;

    /**
     * Returns the name of an email if it is valid
     *
     * @param string $email Email name
     * @return null|string
     */
    public function find($email)
    {
        if ($this->validEmail($email)) {
            return $email;
        }

        return null;
    }

    /**
     * Returns a list of valid emails
     *
     * @return array
     */
    public function getEmails()
    {
        $emails = [];
        foreach (get_class_methods($this) as $methodName) {
            if (!$this->validEmail($methodName)) {
                continue;
            }

            $emails[] = $methodName;
        }

        return $emails;
    }

    /**
     * Returns the name of this preview
     *
     * @return string
     */
    public function name()
    {
        $classname = static::class;
        $pos = strrpos($classname, '\\');

        return substr($classname, $pos + 1);
    }

    /**
     * Returns whether or not a specified email is valid
     * for this MailPreview instance
     *
     * @param string $email Name of email
     * @return bool
     */
    protected function validEmail($email)
    {
        if (empty($email)) {
            return false;
        }

        $baseClass = new ReflectionClass(self::class);
        if ($baseClass->hasMethod($email)) {
            return false;
        }

        try {
            $method = new ReflectionMethod($this, $email);
        } catch (ReflectionException $e) {
            return false;
        }

        return $method->isPublic();
    }
}

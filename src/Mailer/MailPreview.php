<?php
namespace DebugKit\Mailer;

use Cake\Mailer\MailerAwareTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class MailPreview
{
    use MailerAwareTrait;

    /**
     * Returns the name of an email if it is valid
     *
     * @param string $email Email name
     * @return bool|string
     **/
    public function find($email)
    {
        if ($this->validEmail($email)) {
            return $email;
        }

        return false;
    }

    /**
     * Returns a list of valid emails
     *
     * @return array
     **/
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
     **/
    public function name()
    {
        $classname = get_class($this);
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }

        return $pos;
    }

    /**
     * Returns whether or not a specified email is valid
     * for this MailPreview instance
     *
     * @param string $email Name of email
     * @return bool
     **/
    protected function validEmail($email)
    {
        if (empty($email)) {
            return false;
        }

        $baseClass = new ReflectionClass(get_class());
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

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
namespace DebugKit\Mailer;

use Cake\Mailer\Mailer;
use ReflectionClass;

/**
 * Represents the result of an already sent email
 *
 */
class SentMailResult extends AbstractResult
{

    /**
     * Processes the mailer to extract the headers and parts
     *
     * @param array $headers The headers included in the email
     * @param array $parts The rendered parts in the email
     */
    public function __construct(array $headers, array $parts)
    {
        $this->headers = $headers;
        $this->parts = $parts;
    }
}

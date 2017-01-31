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

/**
 * A simple structure for representing the results of a sent email
 */
abstract class AbstractResult
{

    /**
     * The list of headers included in the email
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The rendered parts of the email (for example text and html)
     *
     * @var array
     */
    protected $parts = [];

    /**
     * Returns the list of headers included in th email
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the rendered parts in th email
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}

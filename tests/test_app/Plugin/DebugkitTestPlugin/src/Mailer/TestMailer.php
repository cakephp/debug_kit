<?php
/**
 * Test Panel of test_app
 *
 * PHP 5
 *
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
namespace DebugkitTestPlugin\Mailer;

use Cake\Mailer\Email;
use Cake\Mailer\Mailer;

class TestMailer extends Mailer
{

    /**
     * Test email method
     */
    public function test_email()
    {
        if (!Email::configTransport('default')) {
            Email::configTransport(['default' => ['className' => 'Debug']]);
        }

        return $this;
    }

    public function contain_app_array_url()
    {
        return $this->template("contain_app_array_url")
            ->setLayout(false);
    }

    public function contain_plugin_array_url()
    {
        return $this->template("contain_plugin_array_url")
            ->setLayout(false);
    }
}

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
namespace DebugkitTestPlugin\Mailer\Preview;

use DebugKit\Mailer\MailPreview;

class TestMailerPreview extends MailPreview
{

    /**
     * Test email
     */
    public function test_email()
    {
        return $this->getMailer('DebugkitTestPlugin.Test')->test_email();
    }

    public function contain_app_array_url()
    {
        return $this->getMailer('DebugkitTestPlugin.Test')->contain_app_array_url();
    }

    public function contain_plugin_array_url()
    {
        return $this->getMailer('DebugkitTestPlugin.Test')->contain_plugin_array_url();
    }
}

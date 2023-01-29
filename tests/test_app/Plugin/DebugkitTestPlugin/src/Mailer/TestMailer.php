<?php
declare(strict_types=1);

/**
 * Test Panel of test_app
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugkitTestPlugin\Mailer;

use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

class TestMailer extends Mailer
{
    /**
     * Test email method
     */
    public function test_email()
    {
        if (!TransportFactory::getConfig('default')) {
            TransportFactory::setConfig(['default' => ['className' => 'Debug']]);
        }

        return $this;
    }
}

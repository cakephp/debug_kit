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
 * @since         DebugKit 0.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\TestApp\Panel;

use Cake\Controller\Controller;
use DebugKit\DebugPanel;

/**
 * Class TestPanel
 *
 * @since         DebugKit 0.1
 */
class TestPanel extends DebugPanel
{
    /**
     * Startup
     *
     * @param Controller $controller
     */
    public function startup(Controller $controller)
    {
        $controller->testPanel = true;
    }
}

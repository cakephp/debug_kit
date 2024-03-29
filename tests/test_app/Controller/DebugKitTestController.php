<?php
declare(strict_types=1);

/**
 * DebugKit TestController of test_app
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
 **/
namespace DebugKit\TestApp\Controller;

use Cake\Controller\Controller;

/**
 * Class DebugKitTestController
 *
 * @since         DebugKit 0.1
 */
class DebugKitTestController extends Controller
{
    /**
     * Mame of the Controller
     *
     * @var string
     */
    public $name = 'DebugKitTest';

    /**
     * Uses no Models
     *
     * @var array
     */
    public $uses = [];

    /**
     * Uses only DebugKit Toolbar Component
     *
     * @var array
     */
    public $components = ['DebugKit.Toolbar'];

    /**
     * Return Request Action Value
     *
     * @return string
     */
    public function request_action_return()
    {
        $this->autoRender = false;

        return 'I am some value from requestAction.';
    }

    /**
     * Render Request Action
     */
    public function request_action_render()
    {
        $this->set('test', 'I have been rendered.');
    }
}

<?php
/**
 * ErrorHandler class
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.5
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugKit\Error;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Error\ErrorHandler as BaseErrorHandler;
use Cake\Http\ResponseEmitter;
use DebugKit\ToolbarService;
use Exception;
use Throwable;

/**
 * @see \Cake\Error\ExceptionRenderer for more information
 */
class ErrorHandler extends BaseErrorHandler
{

    /**
     * Display an error.
     *
     * Template method of BaseErrorHandler.
     *
     * Only when debug > 2 will a formatted error be displayed.
     *
     * @param array $error An array of error data.
     * @param bool $debug Whether or not the app is in debug mode.
     * @return void
     */
    protected function _displayError($error, $debug)
    {
        if (!$debug) {
            return;
        }
        if (strtolower($error['error']) !== 'deprecated'
        || !Plugin::loaded('DebugKit')
        || !Configure::read('debug')) {
            parent::_displayError($error, $debug);
        }

        ToolbarService::addDeprecatedError($error);
    }
}

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
namespace DebugKit\Panel;

use Cake\Log\Log;
use DebugKit\DebugPanel;

/**
 * Log Panel - Reads log entries made this request.
 */
class LogPanel extends DebugPanel
{
    /**
     * Initialize hook - sets up the log listener.
     *
     * @return void
     */
    public function initialize()
    {
        if (Log::getConfig('debug_kit_log_panel')) {
            return;
        }
        Log::setConfig('debug_kit_log_panel', [
            'engine' => 'DebugKit.DebugKit',
        ]);
    }

    /**
     * Get the panel data
     *
     * @return array
     */
    public function data()
    {
        return [
            'logger' => Log::engine('debug_kit_log_panel'),
        ];
    }

    /**
     * Get the summary data.
     *
     * @return string
     */
    public function summary()
    {
        /** @var \DebugKit\Log\Engine\DebugKitLog|null $logger */
        $logger = Log::engine('debug_kit_log_panel');
        if (!$logger) {
            return '0';
        }

        return (string)$logger->count();
    }
}

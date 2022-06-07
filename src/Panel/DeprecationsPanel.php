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

use Cake\Event\EventInterface;
use Cake\Utility\Hash;
use DebugKit\DebugInclude;
use DebugKit\DebugPanel;

/**
 * Provides a list of deprecated methods for the current request
 */
class DeprecationsPanel extends DebugPanel
{
    /**
     * The list of depreated errors.
     *
     * @var array
     */
    protected static $deprecatedErrors = [];

    /**
     * instance of DebugInclude
     *
     * @var \DebugKit\DebugInclude
     */
    protected $_debug;

    /**
     * construct
     */
    public function __construct()
    {
        $this->_debug = new DebugInclude();
    }

    /**
     * Get a list of files that were deprecated and split them out into the various parts of the app
     *
     * @return array
     */
    protected function _prepare()
    {
        $errors = static::$deprecatedErrors;
        $return = ['cake' => [], 'app' => [], 'plugins' => [], 'vendor' => [], 'other' => []];

        foreach ($errors as $error) {
            $file = $error['file'];
            $line = $error['line'];

            $errorData = [
                'file' => $file,
                'line' => $line,
                'message' => $error['message'],
            ];

            $pluginName = $this->_debug->getPluginName($file);
            if ($pluginName) {
                $errorData['niceFile'] = $this->_debug->niceFileName($file, 'plugin', $pluginName);
                $return['plugins'][$pluginName][] = $errorData;
            } elseif ($this->_debug->isAppFile($file)) {
                $errorData['niceFile'] = $this->_debug->niceFileName($file, 'app');
                $return['app'][] = $errorData;
            } elseif ($this->_debug->isCakeFile($file)) {
                $errorData['niceFile'] = $this->_debug->niceFileName($file, 'cake');
                $return['cake'][] = $errorData;
            } else {
                $vendorName = $this->_debug->getComposerPackageName($file);

                if ($vendorName) {
                    $errorData['niceFile'] = $this->_debug->niceFileName($file, 'vendor', $vendorName);
                    $return['vendor'][$vendorName][] = $errorData;
                } else {
                    $errorData['niceFile'] = $this->_debug->niceFileName($file, 'root');
                    $return['other'][] = $errorData;
                }
            }
        }

        /** @psalm-suppress RedundantFunctionCall */
        ksort($return['app']);
        /** @psalm-suppress RedundantFunctionCall */
        ksort($return['cake']);
        /** @psalm-suppress RedundantFunctionCall */
        ksort($return['plugins']);
        /** @psalm-suppress RedundantFunctionCall */
        ksort($return['vendor']);

        foreach ($return['plugins'] as &$plugin) {
            /** @psalm-suppress RedundantFunctionCall */
            ksort($plugin);
        }

        return $return;
    }

    /**
     * Add a error
     *
     * @param array $error The deprecated error
     * @return void
     */
    public static function addDeprecatedError($error)
    {
        static::$deprecatedErrors[] = $error;
    }

    /**
     * Reset the tracked errors.
     *
     * @return void
     */
    public static function clearDeprecatedErrors()
    {
        static::$deprecatedErrors = [];
    }

    /**
     * Get the number of files deprecated in this request.
     *
     * @return string
     */
    public function summary()
    {
        $data = $this->_data;
        if (empty($data)) {
            $data = $this->_prepare();
        }

        return (string)array_reduce($data, function ($carry, $item) {
            if (empty($item)) {
                return $carry;
            }
            // app, cake, or other groups
            if (Hash::dimensions($item) == 2) {
                return $carry + count($item);
            }

            // plugin and vendor groups
            foreach ($item as $group) {
                $carry += count($group);
            }

            return $carry;
        }, 0);
    }

    /**
     * Shutdown callback
     *
     * @param \Cake\Event\EventInterface $event Event
     * @return void
     */
    public function shutdown(EventInterface $event)
    {
        $this->_data = $this->_prepare();
    }
}

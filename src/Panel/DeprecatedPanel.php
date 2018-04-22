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
namespace DebugKit\Panel;

use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Composer\Json\JsonFile;
use DebugKit\DebugInclude;
use DebugKit\DebugPanel;

/**
 * Provides a list of deprecated methods for the current request
 *
 */
class DeprecatedPanel extends DebugPanel
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
            $description = $error['message'];
            $line = $error['context']['frame']['line'];
            $file = $error['context']['frame']['file'];

            $pluginName = $this->_debug->getPluginName($file);
            $description = sprintf(
                "(line: %s) \n  %s",
                $line,
                $description
            );
            $description = " " . $description;
            if ($pluginName) {
                $return['plugins'][$pluginName][$this->_debug->getFileType($file)][$this->_debug->niceFileName($file, 'plugin', $pluginName)][] = $description;
            } elseif ($this->_debug->isAppFile($file)) {
                $return['app'][$this->_debug->getFileType($file)][$this->_debug->niceFileName($file, 'app')][] = $description;
            } elseif ($this->_debug->isCakeFile($file)) {
                $return['cake'][$this->_debug->getFileType($file)][$this->_debug->niceFileName($file, 'cake')][] = $description;
            } else {
                $vendorName = $this->_debug->getComposerPackageName($file);

                if ($vendorName) {
                    $return['vendor'][$vendorName][$this->_debug->niceFileName($file, 'vendor', $vendorName)][] = $description;
                } else {
                    $return['other'][$this->_debug->niceFileName($file, 'root')][] = $description;
                }
            }
        }

        ksort($return['app']);
        ksort($return['cake']);
        ksort($return['plugins']);
        ksort($return['vendor']);

        foreach ($return['plugins'] as &$plugin) {
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
        $data = array_filter($data, function ($v, $k) {
            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);

        return count(Hash::flatten($data));
    }

    /**
     * Shutdown callback
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function shutdown(Event $event)
    {
        $this->_data = $this->_prepare();
    }
}

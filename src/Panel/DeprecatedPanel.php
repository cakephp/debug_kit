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
use DebugKit\IncludePanel as basePanel;

/**
 * Provides a list of deprecated methods for the current request
 *
 */
class DeprecatedPanel extends basePanel
{
    /**
     * Get a list of files that were deprecated and split them out into the various parts of the app
     *
     * @return array
     */
    protected function _prepare()
    {
        $errors = \DebugKit\ToolbarService::getDeprecatedErrors();
        $return = ['cake' => [], 'app' => [], 'plugins' => [], 'vendor' => [], 'other' => []];

        foreach ($errors as $error) {
            $description = $error['message'];
            $line = $error['context']['frame']['line'];
            $file = $error['context']['frame']['file'];

            $pluginName = $this->_getPluginName($file);
            $description = sprintf(
                "(line: %s) \n  %s",
                $line,
                $description
            );
            $description = " " . $description;
            if ($pluginName) {
                $return['plugins'][$pluginName][$this->_getFileType($file)][$this->_niceFileName($file, 'plugin', $pluginName)][] = $description;
            } elseif ($this->_isAppFile($file)) {
                $return['app'][$this->_getFileType($file)][$this->_niceFileName($file, 'app')][] = $description;
            } elseif ($this->_isCakeFile($file)) {
                $return['cake'][$this->_getFileType($file)][$this->_niceFileName($file, 'cake')][] = $description;
            } else {
                $vendorName = $this->_getComposerPackageName($file);

                if ($vendorName) {
                    $return['vendor'][$vendorName][$this->_niceFileName($file, 'vendor', $vendorName)][] = $description;
                } else {
                    $return['other'][$this->_niceFileName($file, 'root')][] = $description;
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
}

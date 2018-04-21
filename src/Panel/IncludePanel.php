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
 * Provides a list of included files for the current request
 *
 */
class IncludePanel extends basePanel
{
    /**
     * Get a list of files that were included and split them out into the various parts of the app
     *
     * @return array
     */
    protected function _prepare()
    {
        $return = ['cake' => [], 'app' => [], 'plugins' => [], 'vendor' => [], 'other' => []];

        foreach (get_included_files() as $file) {
            $pluginName = $this->_getPluginName($file);

            if ($pluginName) {
                $return['plugins'][$pluginName][$this->_getFileType($file)][] = $this->_niceFileName($file, 'plugin', $pluginName);
            } elseif ($this->_isAppFile($file)) {
                $return['app'][$this->_getFileType($file)][] = $this->_niceFileName($file, 'app');
            } elseif ($this->_isCakeFile($file)) {
                $return['cake'][$this->_getFileType($file)][] = $this->_niceFileName($file, 'cake');
            } else {
                $vendorName = $this->_getComposerPackageName($file);

                if ($vendorName) {
                    $return['vendor'][$vendorName][] = $this->_niceFileName($file, 'vendor', $vendorName);
                } else {
                    $return['other'][] = $this->_niceFileName($file, 'root');
                }
            }
        }

        $return['paths'] = $this->_includePaths();

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
     * Get the number of files included in this request.
     *
     * @return string
     */
    public function summary()
    {
        $data = $this->_data;
        if (empty($data)) {
            $data = $this->_prepare();
        }

        unset($data['paths']);

        return count(Hash::flatten($data));
    }
}

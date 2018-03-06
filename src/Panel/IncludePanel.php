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
use DebugKit\DebugPanel;

/**
 * Provides a list of included files for the current request
 *
 */
class IncludePanel extends DebugPanel
{

    /**
     * The list of plugins within the application
     *
     * @var string[]
     */
    protected $_pluginPaths = [];

    /**
     * The list of Composer packages
     *
     * @var string[]
     */
    protected $_composerPaths = [];

    /**
     * File Types
     *
     * @var array
     */
    protected $_fileTypes = [
        'Auth', 'Cache', 'Collection', 'Config', 'Configure', 'Console', 'Component', 'Controller',
        'Behavior', 'Database', 'Datasource', 'Model', 'Template', 'View', 'Utility',
        'Network', 'Routing', 'I18n', 'Log', 'Error', 'Event', 'Form', 'Filesystem',
        'ORM', 'Filter', 'Validation'
    ];

    /**
     * Get a list of plugins on construct for later use
     */
    public function __construct()
    {
        foreach (Plugin::loaded() as $plugin) {
            $this->_pluginPaths[$plugin] = str_replace('/', DIRECTORY_SEPARATOR, Plugin::path($plugin));
        }

        $lockFile = new JsonFile(ROOT . DIRECTORY_SEPARATOR . 'composer.lock');
        if ($lockFile->exists()) {
            $lockContent = $lockFile->read();

            $vendorDir = ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
            $packages = array_merge($lockContent['packages'], $lockContent['packages-dev']);

            foreach ($packages as $package) {
                $this->_composerPaths[$package['name']] = $vendorDir . str_replace('/', DIRECTORY_SEPARATOR, $package['name']) . DIRECTORY_SEPARATOR;
            }
        }
    }

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
     * Get the possible include paths
     *
     * @return array
     */
    protected function _includePaths()
    {
        $paths = array_flip(array_filter(explode(PATH_SEPARATOR, get_include_path())));

        unset($paths['.']);

        return array_flip($paths);
    }

    /**
     * Check if a path is part of CakePHP
     *
     * @param string $file File to check
     * @return bool
     */
    protected function _isCakeFile($file)
    {
        return strpos($file, CAKE) === 0;
    }

    /**
     * Check if a path is from APP but not a plugin
     *
     * @param string $file File to check
     * @return bool
     */
    protected function _isAppFile($file)
    {
        return strpos($file, APP) === 0;
    }

    /**
     * Detect plugin the file belongs to
     *
     * @param string $file File to check
     * @return string|bool plugin name, or false if not plugin
     */
    protected function _getPluginName($file)
    {
        foreach ($this->_pluginPaths as $plugin => $path) {
            if (strpos($file, $path) === 0) {
                return $plugin;
            }
        }

        return false;
    }

    /**
     * Detect Composer package the file belongs to
     *
     * @param string $file File to check
     * @return string|bool package name, or false if not Composer package
     */
    protected function _getComposerPackageName($file)
    {
        foreach ($this->_composerPaths as $package => $path) {
            if (strpos($file, $path) === 0) {
                return $package;
            }
        }

        return false;
    }

    /**
     * Replace the path with APP, CAKE, ROOT or the plugin name
     *
     * @param string $file File to check
     * @param string $type The file type
     * @param string|null $name plugin name or composer package
     * @return string Path with replaced prefix
     */
    protected function _niceFileName($file, $type, $name = null)
    {
        switch ($type) {
            case 'app':
                return str_replace(APP, 'APP' . DIRECTORY_SEPARATOR, $file);

            case 'cake':
                return str_replace(CAKE, 'CAKE' . DIRECTORY_SEPARATOR, $file);

            case 'root':
                return str_replace(ROOT, 'ROOT', $file);

            case 'plugin':
                return str_replace($this->_pluginPaths[$name], $name . DIRECTORY_SEPARATOR, $file);

            case 'vendor':
                return str_replace($this->_composerPaths[$name], '', $file);
        }
    }

    /**
     * Get the type of file (model, controller etc)
     *
     * @param string $file File to check.
     * @return string
     */
    protected function _getFileType($file)
    {
        foreach ($this->_fileTypes as $type) {
            if (stripos($file, DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR) !== false) {
                return $type;
            }
        }

        return 'other';
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

<?php
declare(strict_types=1);

/**
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
namespace DebugKit;

use Cake\Core\Plugin as CorePlugin;
use Composer\Json\JsonFile;
use InvalidArgumentException;

/**
 * Contains methods for Providing list of files.
 */
class DebugInclude
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
        'ORM', 'Filter', 'Validation',
    ];

    /**
     * Get a list of plugins on construct for later use
     */
    public function __construct()
    {
        foreach (CorePlugin::loaded() as $plugin) {
            $this->_pluginPaths[$plugin] = str_replace('/', DIRECTORY_SEPARATOR, CorePlugin::path($plugin));
        }

        $lockFile = new JsonFile(ROOT . DIRECTORY_SEPARATOR . 'composer.lock');
        if ($lockFile->exists()) {
            $lockContent = $lockFile->read();

            $vendorDir = ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
            $packages = array_merge($lockContent['packages'], $lockContent['packages-dev']);

            foreach ($packages as $package) {
                $this->_composerPaths[$package['name']] = $vendorDir
                    . str_replace('/', DIRECTORY_SEPARATOR, $package['name'])
                    . DIRECTORY_SEPARATOR;
            }
        }
    }

    /**
     * Get the possible include paths
     *
     * @return array
     */
    public function includePaths()
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        $paths = array_filter($paths, function ($path) {
            if ($path === '.' || strlen($path) === 0) {
                return false;
            }

            return true;
        });

        return array_values($paths);
    }

    /**
     * Check if a path is part of CakePHP
     *
     * @param string $file File to check
     * @return bool
     */
    public function isCakeFile($file)
    {
        return strpos($file, CAKE) === 0;
    }

    /**
     * Check if a path is from APP but not a plugin
     *
     * @param string $file File to check
     * @return bool
     */
    public function isAppFile($file)
    {
        return strpos($file, APP) === 0;
    }

    /**
     * Detect plugin the file belongs to
     *
     * @param string $file File to check
     * @return string|bool plugin name, or false if not plugin
     */
    public function getPluginName($file)
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
    public function getComposerPackageName($file)
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
    public function niceFileName($file, $type, $name = null)
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

        throw new InvalidArgumentException("Type `{$type}` is not supported.");
    }

    /**
     * Get the type of file (model, controller etc)
     *
     * @param string $file File to check.
     * @return string
     */
    public function getFileType($file)
    {
        foreach ($this->_fileTypes as $type) {
            if (stripos($file, DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR) !== false) {
                return $type;
            }
        }

        return 'other';
    }
}

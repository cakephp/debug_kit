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
namespace DebugKit\Panel;

use Cake\Error\Debugger;
use Cake\Event\EventInterface;
use DebugKit\DebugInclude;
use DebugKit\DebugPanel;

/**
 * Provides information about your PHP and CakePHP environment to assist with debugging.
 */
class EnvironmentPanel extends DebugPanel
{
    /**
     * instance of DebugInclude
     *
     * @var \DebugKit\DebugInclude
     */
    protected DebugInclude $_debug;

    /**
     * construct
     */
    public function __construct()
    {
        $this->_debug = new DebugInclude();
    }

    /**
     * Get necessary data about environment to pass back to controller
     *
     * @return array
     */
    protected function _prepare(): array
    {
        $return = [];
        // PHP Data
        $phpVer = phpversion();
        $return['php'] = array_merge(
            ['PHP_VERSION' => $phpVer],
            $_SERVER,
        );
        unset($return['php']['argv']);

        // ini Data
        $return['ini'] = [
            'intl.default_locale' => ini_get('intl.default_locale'),
            'memory_limit' => ini_get('memory_limit'),
            'error_reporting' => ini_get('error_reporting'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'zend.assertions' => ini_get('zend.assertions'),
        ];

        // CakePHP Data
        $return['cake'] = [
            'APP' => APP,
            'APP_DIR' => APP_DIR,
            'CACHE' => CACHE,
            'CAKE' => CAKE,
            'CAKE_CORE_INCLUDE_PATH' => CAKE_CORE_INCLUDE_PATH,
            'CONFIG' => CONFIG,
            'CORE_PATH' => CORE_PATH,
            'DS' => DS,
            'LOGS' => LOGS,
            'ROOT' => ROOT,
            'TESTS' => TESTS,
            'TMP' => TMP,
            'WWW_ROOT' => WWW_ROOT,
        ];

        $hiddenCakeConstants = array_fill_keys(
            [
                'TIME_START', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR',
            ],
            '',
        );
        $var = get_defined_constants(true);
        $return['app'] = array_diff_key($var['user'], $return['cake'], $hiddenCakeConstants);

        $includePaths = $this->_debug->includePaths();
        foreach ($includePaths as $k => $v) {
            $includePaths[$k] = Debugger::exportVarAsNodes($v);
        }

        // Included files data
        $return['includePaths'] = $includePaths;
        $return['includedFiles'] = $this->prepareIncludedFiles();

        return $return;
    }

    /**
     * Shutdown callback
     *
     * @param \Cake\Event\EventInterface $event Event
     * @return void
     */
    public function shutdown(EventInterface $event): void
    {
        $this->_data = $this->_prepare();
    }

    /**
     * Build the list of files segmented by app, cake, plugins, vendor and other
     *
     * @return array
     */
    protected function prepareIncludedFiles(): array
    {
        $return = ['cake' => [], 'app' => [], 'plugins' => [], 'vendor' => [], 'other' => []];

        foreach (get_included_files() as $file) {
            /** @var string|false $pluginName */
            $pluginName = $this->_debug->getPluginName($file);

            if ($pluginName) {
                $return['plugins'][$pluginName][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName(
                    $file,
                    'plugin',
                    $pluginName,
                );
            } elseif ($this->_debug->isAppFile($file)) {
                $return['app'][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName($file, 'app');
            } elseif ($this->_debug->isCakeFile($file)) {
                $return['cake'][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName($file, 'cake');
            } else {
                /** @var string|false $vendorName */
                $vendorName = $this->_debug->getComposerPackageName($file);

                if ($vendorName) {
                    $return['vendor'][$vendorName][] = $this->_debug->niceFileName($file, 'vendor', $vendorName);
                } else {
                    $return['other'][] = $this->_debug->niceFileName($file, 'root');
                }
            }
        }

        $return['paths'] = $this->_debug->includePaths();

        ksort($return['app']);
        ksort($return['cake']);
        ksort($return['plugins']);
        ksort($return['vendor']);

        foreach ($return['plugins'] as &$plugin) {
            ksort($plugin);
        }

        foreach ($return as $k => $v) {
            $return[$k] = Debugger::exportVarAsNodes($v);
        }

        return $return;
    }
}

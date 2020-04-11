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
 * Provides a list of included files for the current request
 */
class IncludePanel extends DebugPanel
{
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
     * Get a list of files that were included and split them out into the various parts of the app
     *
     * @return array
     */
    protected function _prepare()
    {
        $return = ['cake' => [], 'app' => [], 'plugins' => [], 'vendor' => [], 'other' => []];

        foreach (get_included_files() as $file) {
            $pluginName = $this->_debug->getPluginName($file);

            if ($pluginName) {
                $return['plugins'][$pluginName][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName(
                    $file,
                    'plugin',
                    $pluginName
                );
            } elseif ($this->_debug->isAppFile($file)) {
                $return['app'][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName($file, 'app');
            } elseif ($this->_debug->isCakeFile($file)) {
                $return['cake'][$this->_debug->getFileType($file)][] = $this->_debug->niceFileName($file, 'cake');
            } else {
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
        $data = array_filter($data, function ($v, $k) {
            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);

        return (string)count(Hash::flatten($data));
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

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

use Cake\Core\Plugin;
use Cake\Core\PluginConfig;
use DebugKit\DebugPanel;

/**
 * Provides debug information on the available plugins.
 */
class PluginsPanel extends DebugPanel
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $loadedPluginsCollection = Plugin::getCollection();
        $config = PluginConfig::getAppConfig();

        $this->_data['hasEmptyAppConfig'] = empty($config);
        $plugins = [];

        foreach ($config as $pluginName => $options) {
            $plugins[$pluginName] = [
                'isLoaded' => $loadedPluginsCollection->has($pluginName),
                'onlyDebug' => $options['onlyDebug'] ?? false,
                'onlyCli' => $options['onlyCli'] ?? false,
                'optional' => $options['optional'] ?? false,
            ];
        }

        $this->_data['plugins'] = $plugins;
    }

    /**
     * Get summary data for the plugins panel.
     *
     * @return string
     */
    public function summary(): string
    {
        if (!isset($this->_data['plugins'])) {
            return '0';
        }

        return (string)count($this->_data['plugins']);
    }
}

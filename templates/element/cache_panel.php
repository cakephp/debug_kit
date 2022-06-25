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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $metrics
 * @var array $logs
 */
?>
<div class="c-cache-panel">
    <?php if (empty($metrics)) : ?>
        <p class="c-flash c-flash--info"><?= __d('debug_kit', 'There were no cache operations in this request.') ?></p>
    <?php else : ?>
        <h3><?= __d('debug_kit', 'Cache Utilities') ?></h3>
        <table class="c-debug-table">
            <thead>
                <tr>
                    <th><?= __d('debug_kit', 'Engine') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($metrics as $name => $values) : ?>
                <tr>
                    <td><?= h($name) ?></td>
                    <td class="u-text-right">
                        <button
                            class="o-button js-clear-cache"
                            data-name="<?= h($name) ?>"
                            data-url="<?= $this->Url->build([
                                'plugin' => 'DebugKit',
                                'controller' => 'Toolbar',
                                'action' => 'clearCache',
                            ]) ?>"
                            data-csrf="<?= $this->getRequest()
                                ->getAttribute('csrfToken') ?>"
                        >
                            <?= __d('debug_kit', 'Clear All Data') ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="c-cache-panel__messages"></div>

        <h3><?= __d('debug_kit', 'Cache Usage Overview') ?></h3>
        <table class="c-debug-table">
            <thead>
                <tr>
                    <th><?= __d('debug_kit', 'Engine') ?></th>
                    <th><?= __d('debug_kit', 'get hit') ?></th>
                    <th><?= __d('debug_kit', 'get miss') ?></th>
                    <th><?= __d('debug_kit', 'set') ?></th>
                    <th><?= __d('debug_kit', 'delete') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($metrics as $name => $counters) : ?>
                <tr>
                    <td><?= h($name) ?></td>
                    <td class="u-text-right"><?= $counters['get hit'] ?></td>
                    <td class="u-text-right"><?= $counters['get miss'] ?></td>
                    <td class="u-text-right"><?= $counters['set'] ?></td>
                    <td class="u-text-right"><?= $counters['delete'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3><?= __d('debug_kit', 'Cache Logs') ?></h3>
        <table class="c-debug-table">
            <thead>
                <tr>
                    <th><?= __d('debug_kit', 'Log') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log) : ?>
            <tr>
                <td><?= h($log) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

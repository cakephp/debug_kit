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
 * @since         DebugKit 3.5.2
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $packages
 */
?>
<div class="c-packages-panel"
     data-base-url="<?= $this->Url->build([
         'plugin' => 'DebugKit',
         'controller' => 'Composer',
         'action' => 'checkDependencies',
     ]) ?>"
     data-csrf-token="<?= $this->getRequest()->getAttribute('csrfToken') ?>"
    >
    <?php if (empty($packages) && empty($devPackages)) : ?>
        <div class="c-flash c-flash--warning">
            <?= __d('debug_kit', '{0} not found', "'composer.lock'"); ?>
        </div>
    <?php else : ?>
        <div class="c-packages-panel__check-update">
            <button class="o-button"><?= __d('debug_kit', 'Check for Updates') ?></button>
            <label><input type="checkbox"><?= __d('debug_kit', 'Direct dependencies only') ?></label>
        </div>
        <div class="c-packages-panel__terminal">
            <div class="c-packages-panel__terminal-loader">
                <?= __d('debug_kit', 'Loading') .
                    $this->Html->image('DebugKit./img/cake.icon.png', ['class' => 'indicator']) ?>
            </div>
        </div>
        <div class="c-packages-panel__section-wrapper">
            <?php if (!empty($packages)) : ?>
                <section>
                    <h3><?= __d('debug_kit', 'Requirements ({0})', count($packages)) ?> </h3>
                    <table class="c-debug-table">
                        <thead>
                        <tr>
                            <th><?= __d('debug_kit', 'Name') ?></th>
                            <th><?= __d('debug_kit', 'Version') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($packages as $package) : ?>
                            <?php extract($package); ?>
                            <tr>
                                <td title="<?= h($description) ?>">
                                    <a href="https://packagist.org/packages/<?= h($name) ?>"
                                       title="<?= h($description) ?>"
                                       target="_blank"
                                       class="c-packages-panel__link">
                                        <?= h($name) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="c-packages-panel__version"><?= h($version) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
            <?php if (!empty($devPackages)) : ?>
                <section>
                    <h3><?= __d('debug_kit', 'Dev Requirements ({0})', count($devPackages)) ?> </h3>
                    <table class="c-debug-table">
                        <thead>
                        <tr>
                            <th><?= __d('debug_kit', 'Name') ?></th>
                            <th><?= __d('debug_kit', 'Version') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($devPackages as $package) : ?>
                            <?php extract($package); ?>
                            <tr>
                                <td title="<?= h($description) ?>">
                                    <a href="https://packagist.org/packages/<?= h($name) ?>"
                                       title="<?= h($description) ?>"
                                       target="_blank"
                                       class="c-packages-panel__link">
                                        <?= h($name) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="c-packages-panel__version"><?= h($version) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

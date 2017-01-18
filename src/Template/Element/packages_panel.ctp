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
 * @type \DebugKit\View\AjaxView $this
 * @type array $packages
 */
?>
<?php if (empty($packages) && empty($devPackages)): ?>
    <div class="warning">
        <?= __d('debug_kit', '{0} not found', "'composer.lock'"); ?>
    </div>
<?php else: ?>
    <?php if (!empty($packages)): ?>
        <section class="section-tile">
            <h3><?= __d('debug_kit', 'Requirements ({0})', count($packages)) ?> </h3>
            <table cellspacing="0" cellpadding="0" class="debug-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Version</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($packages as $package): ?>
                    <?php extract($package); ?>
                    <tr>
                        <td title="<?= h($description) ?>">
                            <a href="https://packagist.org/packages/<?= h($name) ?>" title="<?= h($description) ?>" target="_blank" class="package-link"><?= h($name) ?></a>
                        </td>
                        <td>
                            <span class="package-version"><span class="panel-summary"><?= h($version) ?></span></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
    <?php if (!empty($devPackages)): ?>
        <section class="section-tile">
            <h3><?= __d('debug_kit', 'Dev Requirements ({0})', count($devPackages)) ?> </h3>
            <table cellspacing="0" cellpadding="0" class="debug-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Version</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($devPackages as $package): ?>
                    <?php extract($package); ?>
                    <tr>
                        <td title="<?= h($description) ?>">
                            <a href="https://packagist.org/packages/<?= h($name) ?>" title="<?= h($description) ?>" target="_blank" class="package-link"><?= h($name) ?></a>
                        </td>
                        <td>
                            <span class="package-version"><span class="panel-summary"><?= h($version) ?></span></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
<?php endif; ?>

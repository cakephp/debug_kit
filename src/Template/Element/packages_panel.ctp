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

<?php if (!empty($packages)): ?>
    <?php foreach ($packages as $title => $requirements): ?>
    <section class="section-tile">
        <h3><?= __d('debug_kit', $title) ?> </h3>
        <table cellspacing="0" cellpadding="0" class="debug-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Version</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($requirements as $package): ?>
                <?php extract($package); ?>
                <tr>
                    <td title="<?= $description ?>">
                        <a href="https://packagist.org/packages/<?= $name ?>" title="<?= $description ?>" target="_blank" class="package-link"><?= $name ?></a>
                    </td>
                    <td>
                        <span class="package-version"><span class="panel-summary"><?= $version ?></span></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endforeach; ?>
<?php else: ?>
    <div class="warning">
        <?= __d('debug_kit', "'composer.lock' not found"); ?>
    </div>
<?php endif; ?>

<?php
/**
 * Environment Panel Element
 *
 * Shows information about the current app environment
 *
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
use Cake\Error\Debugger;

/**
 * @type \DebugKit\View\AjaxView $this
 * @type array $app
 * @type array $cake
 * @type array $php
 * @type array $hidef
 */
?>

<h2><?= __d('debug_kit', 'Application Constants') ?></h2>

<?php if (!empty($app)): ?>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
        <tr>
            <th>Constant</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($app as $key => $val): ?>
        <tr>
            <td><?= h($key) ?></td>
            <td><?= h(Debugger::exportVar($val)) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="warning">
    <?= __d('debug_kit', 'No application environment available.'); ?>
</div>
<?php endif; ?>

<h2><?= __d('debug_kit', 'CakePHP Constants') ?></h2>

<?php if (!empty($cake)): ?>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
        <tr>
            <th>Constant</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cake as $key => $val): ?>
        <tr>
            <td><?= h($key) ?></td>
            <td><?= h(Debugger::exportVar($val)) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="warning">
    <?= __d('debug_kit', 'CakePHP environment unavailable.'); ?>
</div>
<?php endif; ?>

<h2><?= __d('debug_kit', 'PHP Environment') ?></h2>

<?php if (!empty($php)): ?>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
        <tr>
            <th>Environment Variable</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($php as $key => $val): ?>
        <tr>
            <td><?= h($key) ?></td>
            <td><?= $this->Credentials->filter($val) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="warning">
    <?= __d('debug_kit', 'PHP environment unavailable.'); ?>
</div>
<?php endif; ?>

<?php if (isset($hidef)): ?>
    <h2><?= __d('debug_kit', 'Hidef Environment') ?></h2>

    <?php if (!empty($hidef)): ?>
    <table cellspacing="0" cellpadding="0" class="debug-table">
        <thead>
            <tr>
                <th>Constant</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hidef as $key => $val): ?>
            <tr>
                <td><?= h($key) ?></td>
                <td><?= h(Debugger::exportVar($val)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="warning">
        <?= __d('debug_kit', 'No Hidef environment available.'); ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php
declare(strict_types=1);

/**
 * Environment Panel Element
 *
 * Shows information about the current app environment
 *
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
use Cake\Core\Configure;
use Cake\Error\Debugger;
use function Cake\Core\h;

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $app
 * @var array $cake
 * @var array $php
 * @var array $includedFiles
 * @var array $includePaths
 * @var \DebugKit\View\Helper\ToolbarHelper $this->Toolbar
 * @var \DebugKit\View\Helper\CredentialsHelper $this->Credentials
 */
?>
<div class="c-environment-panel">
    <h2>CakePHP Version: <?= Configure::version() ?></h2>

    <h3>Application Constants</h3>

    <?php if (!empty($app)) : ?>
        <table class="c-debug-table">
            <thead>
            <tr>
                <th>Constant</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($app as $key => $val) : ?>
                <tr>
                    <td><?= h($key) ?></td>
                    <td><?= Debugger::exportVar($val) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="c-flash c-flash--warning">
            No application environment available.
        </div>
    <?php endif; ?>

    <h3>CakePHP Constants</h3>

    <?php if (!empty($cake)) : ?>
        <table class="c-debug-table">
            <thead>
            <tr>
                <th>Constant</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cake as $key => $val) : ?>
                <tr>
                    <td><?= h($key) ?></td>
                    <td><?= Debugger::exportVar($val) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="c-flash c-flash--warning">
            CakePHP environment unavailable.
        </div>
    <?php endif; ?>

    <h3>INI Environment</h3>

    <?php if (!empty($ini)) : ?>
        <table class="c-debug-table">
            <thead>
            <tr>
                <th>INI Variable</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ini as $key => $val) : ?>
                <tr>
                    <td><?= h($key) ?></td>
                    <td><?= $this->Credentials->filter($val) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="c-flash c-flash--warning">
            ini environment unavailable.
        </div>
    <?php endif; ?>

    <h3>PHP Environment</h3>

    <?php if (!empty($php)) : ?>
        <table class="c-debug-table">
            <thead>
            <tr>
                <th>Environment Variable</th>
                <th>Value</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($php as $key => $val) : ?>
                <tr>
                    <td><?= h($key) ?></td>
                    <td><?= $this->Credentials->filter($val) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="c-flash c-flash--warning">
            PHP environment unavailable.
        </div>
    <?php endif; ?>

    <h3>Included Files</h3>

    <h4>Include Paths</h4>
    <?= $this->Toolbar->dumpNodes($includePaths) ?>

    <h4>Included Files</h4>
    <?= $this->Toolbar->dumpNodes($includedFiles) ?>
</div>

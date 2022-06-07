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
<div class="c-packages-panel">
    <?php if (empty($packages) && empty($devPackages)): ?>
        <div class="c-flash c-flash--warning">
            <?= __d('debug_kit', '{0} not found', "'composer.lock'"); ?>
        </div>
    <?php else: ?>
        <div class="c-packages-panel__check-update">
            <button class="o-button"><?= __d('debug_kit', 'Check for Updates') ?></button>
            <label><input type="checkbox"><?= __d('debug_kit', 'Direct dependencies only') ?></label>
        </div>
        <div class="c-packages-panel__terminal"></div>
        <?php if (!empty($packages)): ?>
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
                    <?php foreach ($packages as $package): ?>
                        <?php extract($package); ?>
                        <tr>
                            <td title="<?= h($description) ?>">
                                <a href="https://packagist.org/packages/<?= h($name) ?>" title="<?= h($description) ?>" target="_blank" class="c-packages-panel__link"><?= h($name) ?></a>
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
        <?php if (!empty($devPackages)): ?>
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
                    <?php foreach ($devPackages as $package): ?>
                        <?php extract($package); ?>
                        <tr>
                            <td title="<?= h($description) ?>">
                                <a href="https://packagist.org/packages/<?= h($name) ?>" title="<?= h($description) ?>" target="_blank" class="c-packages-panel__link"><?= h($name) ?></a>
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
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            var baseUrl = '<?= $this->Url->build([
                'plugin' => 'DebugKit',
                'controller' => 'Composer',
                'action' => 'checkDependencies'
            ]); ?>';

            var terminal = $('.c-packages-panel__terminal');

            function showMessage(el, html) {
                el.show().html(html);
            }

            function buildLoader() {
                return '<div class="loading"><?= __d('debug_kit', 'Loading') . ' ' . $this->Html->image('DebugKit./img/cake.icon.png', ['class' => 'indicator']) ?></div>';
            }

            function buildSuccessfulMessage(response) {
                var html = '';
                if (response.packages.bcBreaks === undefined && response.packages.semverCompatible === undefined) {
                    return '<pre class="c-packages-panel__up2date">All dependencies are up to date</pre>';
                }
                if (response.packages.bcBreaks !== undefined) {
                    html += '<h4 class="c-packages-panel__section-header">Update with potential BC break</h4>';
                    html += '<pre>' + response.packages.bcBreaks + '</pre>';
                }
                if (response.packages.semverCompatible !== undefined) {
                    html += '<h4 class="c-packages-panel__section-header">Update semver compatible</h4>';
                    html += '<pre>' + response.packages.semverCompatible + '</pre>';
                }
                return html;
            }

            function buildErrorMessage(response) {
                return '<pre class="c-packages-panel__warning-message">' + JSON.parse(response.responseText).message + '</pre>';
            }

            $('.c-packages-panel__check-update button').on('click', function(e) {
                showMessage(terminal, buildLoader());
                var direct = $('.c-packages-panel__check-update input')[0].checked;
                var xhr = $.ajax({
                    headers: {'X-CSRF-TOKEN': '<?= $this->request->getAttribute('csrfToken') ?>'},
                    url: baseUrl,
                    data: {direct: direct},
                    dataType: 'json',
                    type: 'POST'
                });
                xhr.done(function(response) {
                    showMessage(terminal, buildSuccessfulMessage(response));
                }).error(function(response) {
                    showMessage(terminal, buildErrorMessage(response));
                });
                e.preventDefault();
            });
        });
    </script>
</div>

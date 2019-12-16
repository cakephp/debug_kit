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
<?php if (empty($packages) && empty($devPackages)): ?>
    <div class="warning">
        <?= __d('debug_kit', '{0} not found', "'composer.lock'"); ?>
    </div>
<?php else: ?>
    <div class="check-update">
        <button class="btn-primary"><?= __d('debug_kit', 'Check for Updates') ?></button>
        <label><input type="checkbox" class="direct-dependency"><?= __d('debug_kit', 'Direct dependencies only') ?></label>
    </div>
    <div class="terminal"></div>
    <?php if (!empty($packages)): ?>
        <section class="section-tile">
            <h3><?= __d('debug_kit', 'Requirements ({0})', count($packages)) ?> </h3>
            <table cellspacing="0" cellpadding="0" class="debug-table">
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
                    <th><?= __d('debug_kit', 'Name') ?></th>
                    <th><?= __d('debug_kit', 'Version') ?></th>
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

<script>
    $(document).ready(function() {
        var baseUrl = '<?= $this->Url->build([
            'plugin' => 'DebugKit',
            'controller' => 'Composer',
            'action' => 'checkDependencies'
        ]); ?>';

        var terminal = $('.terminal');

        function showMessage(el, html) {
            el.show().html(html);
        }

        function buildLoader() {
            return '<div class="loading"><?= __d('debug_kit', 'Loading') . ' ' . $this->Html->image('DebugKit./img/cake.icon.png', ['class' => 'indicator']) ?></div>';
        }

        function buildSuccessfulMessage(response) {
            var html = '';
            if (response.packages.bcBreaks === undefined && response.packages.semverCompatible === undefined) {
                return '<pre class="success-message">All dependencies are up to date</pre>';
            }
            if (response.packages.bcBreaks !== undefined) {
                html += '<h4 class="section-header">Update with potential BC break</h4>';
                html += '<pre>' + response.packages.bcBreaks + '</pre>';
            }
            if (response.packages.semverCompatible !== undefined) {
                html += '<h4 class="section-header">Update semver compatible</h4>';
                html += '<pre>' + response.packages.semverCompatible + '</pre>';
            }
            return html;
        }

        function buildErrorMessage(response) {
            return '<pre class="warning-message">' + JSON.parse(response.responseText).message + '</pre>';
        }

        $('.check-update button').on('click', function(e) {
            showMessage(terminal, buildLoader());
            var direct = $('.direct-dependency')[0].checked;
            var xhr = $.ajax({
                headers: {'X-CSRF-TOKEN': '<?= $this->request->getParam('_csrfToken') ?>'},
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

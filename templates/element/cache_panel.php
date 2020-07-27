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
<?php if (empty($metrics)): ?>
    <p class="info"><?= __d('debug_kit', 'There were no cache operations in this request.') ?></p>
<?php else: ?>
    <h3><?= __d('debug_kit', 'Cache Utilities') ?></h3>
    <table cellspacing="0" cellpadding="0" class="debug-table">
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
                <td class="right-text">
                    <button
                        class="btn-primary clear-cache"
                        data-name="<?= h($name) ?>"
                    >
                        <?= __d('debug_kit', 'Clear All Data') ?>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3><?= __d('debug_kit', 'Cache Usage Overview') ?></h3>
    <table cellspacing="0" cellpadding="0" class="debug-table">
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
                <td class="right-text"><?= $counters['get hit'] ?></td>
                <td class="right-text"><?= $counters['get miss'] ?></td>
                <td class="right-text"><?= $counters['set'] ?></td>
                <td class="right-text"><?= $counters['delete'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3><?= __d('debug_kit', 'Cache Logs') ?></h3>
    <table cellspacing="0" cellpadding="0" class="debug-table">
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

<script>
$(document).ready(function() {
    var baseUrl = '<?= $this->Url->build([
        'plugin' => 'DebugKit',
        'controller' => 'Toolbar',
        'action' => 'clearCache'
    ]); ?>';

    function showMessage(el, text) {
        el.show().text(text).fadeOut(2000);
    }

    $('.clear-cache').on('click', function(e) {
        var el = $(this);
        var name = el.data('name');
        var messageEl = el.parent().find('.inline-message');

        var xhr = $.ajax({
            headers: {'X-CSRF-TOKEN': '<?= $this->request->getAttribute('csrfToken') ?>'},
            url: baseUrl,
            data: {name: name},
            dataType: 'json',
            type: 'POST'
        });
        xhr.done(function(response) {
            showMessage(messageEl, name + ' ' + '<?= __d('debug_kit', 'cache cleared.') ?>');
        }).error(function(response) {
            showMessage(messageEl, name + ' ' + '<?= __d('debug_kit', 'cache could not be cleared.') ?>');
        });
        e.preventDefault();
    });
});
</script>

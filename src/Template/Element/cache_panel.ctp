<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $metrics
 */
?>
<?php if (empty($metrics)): ?>
    <p class="info"><?= __d('debug_kit', 'There were no cache operations this request.') ?></p>
<?php else: ?>
    <?php foreach ($metrics as $name => $counters): ?>
    <section class="section-tile">
        <h3><?= __d('debug_kit', '{0} Metrics', h($name)) ?> </h3>
        <button class="btn-primary clear-cache" data-name="<?= h($name) ?>"><?= __d('debug_kit', 'Clear {0} cache', h($name)) ?></button>
        <span class="inline-message"></span>
        <table cellspacing="0" cellpadding="0" class="debug-table">
            <thead>
                <tr>
                    <th><?= __d('debug_kit', 'Metric') ?></th>
                    <th><?= __d('debug_kit', 'Total') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($counters as $key => $val): ?>
                <tr>
                    <td><?= h($key) ?></td>
                    <td class="right-text"><?= $val ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endforeach; ?>
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
            headers: {'X-CSRF-TOKEN': '<?= $this->request->getParam('_csrfToken') ?>'},
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

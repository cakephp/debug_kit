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
 * @since         DebugKit 3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Routing\Router;
?>
<div id="ajax-history">
    <p class="warning"><?= __d('debug_kit', 'No Ajax requests logged.') ?></p>
</div>

<script type="text/html" id="list-template">
    <ul class="history-list">
        <li>
            <?= $this->Html->link(
                __d('debug_kit', 'Back to current request'),
                ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index', $panel->request_id],
                ['class' => 'history-link', 'data-request' => $panel->request_id]
            ) ?>
        </li>
    </ul>
</script>
<script type="text/html" id="list-item-template">
<li>
    <?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index'] ?>
    <a class="history-link" data-request="{id}" href="<?= $this->Url->build($url) ?>/{id}">
        <span class="history-time">{time}</span>
        <span class="history-bubble">{method}</span>
        <span class="history-bubble">{status}</span>
        <span class="history-url">{url}</span>
    </a>
</li>
</script>
<script>
$(document).ready(function() {
    var toolbar = window.toolbar;

    if (toolbar.ajaxRequests.length === 0) {
        return;
    }

    $('#ajax-history').html($('#list-template').html());
    var listItem = $('#list-item-template').html();

    for (var i = 0; i < toolbar.ajaxRequests.length; i++) {
        var params = {
            id: toolbar.ajaxRequests[i].requestId,
            time: (new Date(toolbar.ajaxRequests[i].date)).toLocaleString(),
            method: toolbar.ajaxRequests[i].method,
            status: toolbar.ajaxRequests[i].status,
            url: toolbar.ajaxRequests[i].url
        };
        var content = listItem.replace(/{([^{}]*)}/g, function(a, b) {
            var r = params[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
        $('ul.history-list').append(content);
    }

    var panelButtons = $('.panel');
    var thisPanel = '<?= h($panel->id) ?>';
    var buttons = $('.history-link');

    // Highlight the active request.
    buttons.filter('[data-request=' + window.toolbar.currentRequest + ']').addClass('active');

    buttons.on('click', function(e) {
        var el = $(this);
        e.preventDefault();
        buttons.removeClass('active');
        el.addClass('active');

        window.toolbar.currentRequest = el.data('request');

        $.getJSON(el.attr('href'), function(response) {
            if (response.panels[0].request_id == window.toolbar.originalRequest) {
                $('#panel-content-container').removeClass('history-mode');
                $('#toolbar').removeClass('history-mode');
            } else {
                $('#panel-content-container').addClass('history-mode');
                $('#toolbar').addClass('history-mode');
            }

            for (var i = 0, len = response.panels.length; i < len; i++) {
                var panel = response.panels[i];
                var button = panelButtons.eq(i);
                var summary = button.find('.panel-summary');

                // Don't overwrite the history panel.
                if (button.data('id') == thisPanel) {
                    continue;
                }
                button.attr('data-id', panel.id);
                summary.text(panel.summary);
            }
        });
    });
});
</script>

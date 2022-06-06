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
 * @since         DebugKit 1.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * @var \DebugKit\View\AjaxView $this
 * @var \DebugKit\Model\Entity\Panel $panel
 * @var array $requests
 */
?>
<div id="request-history">
    <?php if (empty($requests)): ?>
        <p class="warning">
            <?= __d('debug_kit', 'No requests logged.') ?>
            <button type="button" class="js-debugkit-open-panel" data-id="latest-history"><?= __d('debug_kit', 'Reload') ?></button>
        </p>
    <?php else: ?>
        <p>
            <?= count($requests) ?> <?= __d('debug_kit', 'requests available') ?>
            <button type="button" class="js-debugkit-open-panel" data-id="latest-history"><?= __d('debug_kit', 'Reload') ?></button>
        </p>
        <ul class="history-list">
            <li>
                <?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index', $panel->request_id] ?>
                <a class="history-link" data-request="<?= $panel->request_id ?>" href="<?= $this->Url->build($url) ?>">
                    <span class="history-time"><?= h($panel->request->requested_at) ?></span>
                    <span class="history-bubble"><?= h($panel->request->method) ?></span>
                    <span class="history-bubble"><?= h($panel->request->status_code) ?></span>
                    <span class="history-bubble"><?= h($panel->request->content_type) ?></span>
                    <span class="history-url"><?= h($panel->request->url) ?></span>
                </a>
            </li>
            <?php foreach ($requests as $request): ?>
                <?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index', $request->id] ?>
                <li>
                    <a class="history-link" data-request="<?= $request->id ?>" href="<?= $this->Url->build($url) ?>">
                        <span class="history-time"><?= h($request->requested_at) ?></span>
                        <span class="history-bubble"><?= h($request->method) ?></span>
                        <span class="history-bubble"><?= h($request->status_code) ?></span>
                        <span class="history-bubble"><?= h($request->content_type) ?></span>
                        <span class="history-url"><?= h($request->url) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<script type="text/html" id="list-template">
    <p>
        <button type="button" class="js-debugkit-open-panel" data-id="latest-history"><?= __d('debug_kit', 'Reload') ?></button>
    </p>
    <ul class="history-list">
        <li>
            <?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index', $panel->request_id] ?>
            <a class="history-link" data-request="<?= $panel->request_id ?>" href="<?= $this->Url->build($url) ?>">
                <span class="history-time"><?= h($panel->request->requested_at) ?></span>
                <span class="history-bubble"><?= h($panel->request->method) ?></span>
                <span class="history-bubble"><?= h($panel->request->status_code) ?></span>
                <span class="history-bubble"><?= h($panel->request->content_type) ?></span>
                <span class="history-url"><?= h($panel->request->url) ?></span>
            </a>
        </li>
    </ul>
</script>

<script type="text/html" id="list-item-template">
    <li>
        <?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index'] ?>
        <a class="history-link" data-request="{id}" href="<?= $this->Url->build($url) ?>/{id}">
            <span class="history-time">{time}</span>
            <span class="history-bubble xhr">XHR</span>
            <span class="history-bubble">{method}</span>
            <span class="history-bubble">{status}</span>
            <span class="history-bubble">{type}</span>
            <span class="history-url">{url}</span>
        </a>
    </li>
</script>

<script>
    $(document).ready(function() {
        let panelButtons = $('.panel');
        let thisPanel = '<?= h($panel->id) ?>';
        let toolbar = window.debugKitToolbar;

        if (!$('#request-history > ul').length) {
            $('#request-history').html($('#list-template').html());
        }

        let listItem = $('#list-item-template').html();

        for (let i = 0; i < toolbar.ajaxRequests.length; i++) {
            let params = {
                id: toolbar.ajaxRequests[i].requestId,
                time: (new Date(toolbar.ajaxRequests[i].date)).toLocaleString(),
                method: toolbar.ajaxRequests[i].method,
                status: toolbar.ajaxRequests[i].status,
                url: toolbar.ajaxRequests[i].url,
                type: toolbar.ajaxRequests[i].type
            };
            let content = listItem.replace(/{([^{}]*)}/g, function(a, b) {
                let r = params[b];
                return typeof r === 'string' || typeof r === 'number' ? r : a;
            });
            $('ul.history-list li:first').after(content);
        }

        let buttons = $('.history-link');
        // Highlight the active request.
        buttons.filter('[data-request=' + window.debugKitToolbar.currentRequest + ']').addClass('active');

        buttons.on('click', function(e) {
            let el = $(this);
            e.preventDefault();
            buttons.removeClass('active');
            el.addClass('active');

            window.debugKitToolbar.currentRequest = el.data('request');

            $.getJSON(el.attr('href'), function(response) {
                if (response.panels[0].request_id === window.debugKitToolbar.originalRequest) {
                    $('#panel-content-container').removeClass('history-mode');
                    $('#toolbar').removeClass('history-mode');
                } else {
                    $('#panel-content-container').addClass('history-mode');
                    $('#toolbar').addClass('history-mode');
                }

                for (var i = 0, len = response.panels.length; i < len; i++) {
                    let panel = response.panels[i];
                    // Offset by two for scroll buttons
                    let button = panelButtons.eq(i + 2);
                    var summary = button.find('.panel-summary');

                    // Don't overwrite the history panel.
                    if (button.data('id') === thisPanel) {
                        continue;
                    }
                    button.attr('data-id', panel.id);
                    summary.text(panel.summary);
                }
            });
        });
    });
</script>

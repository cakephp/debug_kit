export default (($) => {
  const init = (toolbar) => {
    const $historyPanel = $('.c-history-panel');
    const thisPanel = $historyPanel.attr('data-panel-id');

    if (!$('.c-history-panel > ul').length) {
      $historyPanel.html($('#list-template').html());
    }

    const listItem = $('#list-item-template').html();

    for (let i = 0; i < toolbar.ajaxRequests.length; i++) {
      const element = toolbar.ajaxRequests[i];
      // Request ID is a server-generated UUID; sanitize defensively before
      // substituting into the href/data-request attribute scaffold.
      const safeId = String(element.requestId || '').replace(/[^A-Za-z0-9-]/g, '');

      // Only the {id} placeholder feeds attributes; substitute it now and
      // populate the visible text via .text() so attacker-controlled values
      // (method/URL/Content-Type from cross-origin responses) cannot inject
      // HTML into the toolbar iframe (same-origin with the dev's app).
      const $row = $(listItem.replace(/\{id\}/g, safeId));
      $row.find('.c-history-panel__time').text((new Date(element.date)).toLocaleString());
      // bubble[0] is the static "XHR" label; the next three are method/status/type.
      const $bubbles = $row.find('.c-history-panel__bubble').not('.c-history-panel__xhr');
      $bubbles.eq(0).text(String(element.method ?? ''));
      $bubbles.eq(1).text(String(element.status ?? ''));
      $bubbles.eq(2).text(String(element.type ?? ''));
      $row.find('.c-history-panel__url').text(String(element.url ?? ''));

      $('.c-history-panel__list li:first').after($row);
    }

    const links = $('.c-history-panel__link');
    // Highlight the active request via attribute comparison rather than an
    // unquoted attribute-selector built from a runtime value.
    links.filter(function highlightActive() {
      return this.getAttribute('data-request') === String(toolbar.currentRequest);
    }).addClass('is-active');

    links.on('click', function historyLinkClick(e) {
      const el = $(this);
      e.preventDefault();
      links.removeClass('is-active');
      el.addClass('is-active');

      toolbar.currentRequest = el.attr('data-request');

      $.getJSON(el.attr('href'), (response) => {
        if (response.panels[0].request_id === toolbar.originalRequest) {
          $('body').removeClass('is-history-mode');
        } else {
          $('body').addClass('is-history-mode');
        }

        for (let i = 0, len = response.panels.length; i < len; i++) {
          const panel = response.panels[i];
          const button = toolbar.$panelButtons.eq(i);
          const summary = button.find('.c-panel__summary');

          // Don't overwrite the history panel.
          if (button.data('id') !== thisPanel) {
            button.attr('data-id', panel.id);
            summary.text(panel.summary);
          }
        }
      });
    });
  };

  const onEvent = (toolbar) => {
    document.addEventListener('initPanel', (e) => {
      if (e.detail === 'panelhistory') {
        init(toolbar);
      }
    });
  };

  return {
    onEvent,
  };
})(jQuery);

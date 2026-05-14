export default (($) => {
  const buildSuccessfulMessage = (response) => {
    const $out = $('<div>');
    if (response.packages.bcBreaks === undefined && response.packages.semverCompatible === undefined) {
      $out.append($('<pre>').addClass('c-packages-panel__up2date').text('All dependencies are up to date'));
      return $out;
    }
    if (response.packages.bcBreaks !== undefined) {
      $out.append($('<h4>').addClass('c-packages-panel__section-header').text('Update with potential BC break'));
      $out.append($('<pre>').text(String(response.packages.bcBreaks)));
    }
    if (response.packages.semverCompatible !== undefined) {
      $out.append($('<h4>').addClass('c-packages-panel__section-header').text('Update semver compatible'));
      $out.append($('<pre>').text(String(response.packages.semverCompatible)));
    }
    return $out;
  };

  const showMessage = (el, $content) => {
    el.show().empty().append($content);
    $('.o-loader').removeClass('is-loading');
  };

  const buildErrorMessage = (jqXHR) => {
    let message = '';
    try {
      message = String(JSON.parse(jqXHR.responseText).message ?? '');
    } catch (_e) {
      message = String(jqXHR.responseText || jqXHR.statusText || 'Request failed');
    }
    return $('<pre>').addClass('c-packages-panel__warning-message').text(message);
  };

  const init = () => {
    const $panel = $('.c-packages-panel');
    const baseUrl = $panel.attr('data-base-url');
    const csrfToken = $panel.attr('data-csrf-token');
    const $terminal = $('.c-packages-panel__terminal');

    $('.c-packages-panel__check-update button').on('click', (e) => {
      $('.o-loader').addClass('is-loading');

      const direct = $('.c-packages-panel__check-update input')[0].checked;
      $.ajax({
        headers: { 'X-CSRF-TOKEN': csrfToken },
        url: baseUrl,
        data: { direct },
        dataType: 'json',
        type: 'POST',
        success(data) {
          showMessage($terminal, buildSuccessfulMessage(data));
        },
        error(jqXHR) {
          showMessage($terminal, buildErrorMessage(jqXHR));
        },
      });
      e.preventDefault();
    });
  };

  const onEvent = () => {
    document.addEventListener('initPanel', (e) => {
      if (e.detail === 'panelpackages') {
        init();
      }
    });
  };

  return {
    onEvent,
  };
})(jQuery);

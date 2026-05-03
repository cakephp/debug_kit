export default (($) => {
  const addMessage = (text) => {
    $(`<p>${text}</p>`)
      .appendTo('.c-request-panel__messages')
      .fadeOut(2000);
  };

  const init = () => {
    $('.js-clear-session').on('click', function triggerSessionClear(e) {
      const el = $(this);
      const baseUrl = el.attr('data-url');
      const csrf = el.attr('data-csrf');

      $.ajax({
        headers: { 'X-CSRF-TOKEN': csrf },
        url: baseUrl,
        dataType: 'json',
        type: 'POST',
        success(data) {
          addMessage(data.message);
        },
        error(jqXHR, textStatus, errorThrown) {
          addMessage(errorThrown);
        },
      });
      e.preventDefault();
    });
  };

  const onEvent = () => {
    document.addEventListener('initPanel', (e) => {
      if (e.detail === 'panelrequest') {
        init();
      }
    });
  };

  return {
    onEvent,
  };
})(jQuery);

export default (($) => {
  function init(toolbar) {
    $(document).on('change', '.js-debugkit-sort-variables', function sortVariables() {
      if (!$(this).prop('checked')) {
        document.cookie = `debugKit_sort=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=${window.debugKitWebroot}`;
      } else {
        document.cookie = `debugKit_sort=1; path=${window.debugKitWebroot}`;
      }
      toolbar.loadPanel(toolbar.currentPanel(), 'panelvariables');
    });
  }

  const onEvent = (toolbar) => {
    document.addEventListener('initPanel', (e) => {
      if (e.detail === 'panelvariables') {
        init(toolbar);
      }
    });
  };

  return {
    onEvent,
  };
})(jQuery);

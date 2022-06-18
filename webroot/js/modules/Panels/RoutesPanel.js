export default (($) => {
  const init = () => {
    $(document).on('click', '.js-toggle-plugin-route', function togglePluginRoute() {
      const $this = $(this);
      const plugin = $this.attr('data-plugin');

      if ($this.hasClass('is-active')) {
        $this.removeClass('is-active');
        $(`.c-routes-panel__route-entry${plugin}`).removeClass('is-hidden');
      } else {
        $this.addClass('is-active');
        $(`.c-routes-panel__route-entry${plugin}`).addClass('is-hidden');
      }
    });
  };

  return {
    init,
  };
})(jQuery);

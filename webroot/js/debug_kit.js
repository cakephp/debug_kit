var baseUrl, toolbar;

var elem = document.getElementById('__debug_kit_app');
if (elem) {
  window.__debugKitId = elem.getAttribute('data-id');
  window.__debugKitBaseUrl = elem.getAttribute('data-url');
  window.__debugKitWebroot = elem.getAttribute('data-webroot');
  elem = null;
}

$(document).ready(function() {
  toolbar = new Toolbar({
    toolbar: $('.js-toolbar'),
    container: $('.js-panel-content-container'),
    panelButtons: $('.panel'),
    closeBtn: $('.js-debugkit-panel-close'),
    keyboardScope : $(document),
    currentRequest: __debugKitId,
    originalRequest: __debugKitId,
    baseUrl: __debugKitBaseUrl,
    webroot: __debugKitWebroot,
  });

  toolbar.initialize();
});

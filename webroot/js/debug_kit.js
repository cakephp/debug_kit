var baseUrl, toolbar;

var elem = document.getElementById("__debug_kit_app");
if (elem) {
    window.__debug_kit_id = elem.getAttribute("data-id");
    window.__debug_kit_base_url = elem.getAttribute("data-url");
    window.__debug_kit_webroot = elem.getAttribute("data-webroot");
    elem = null;
}

$(document).ready(function() {
    toolbar = new Toolbar({
        button: $('#toolbar'),
        content: $('#panel-content-container'),
        panelButtons: $('.panel'),
        panelClose: $('#panel-close'),
        keyboardScope : $(document),
        currentRequest: __debug_kit_id,
        originalRequest: __debug_kit_id,
        baseUrl: __debug_kit_base_url,
        webroot: __debug_kit_webroot,
    });

    toolbar.initialize();

});

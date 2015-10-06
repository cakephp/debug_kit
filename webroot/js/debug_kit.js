var baseUrl, toolbar;

var elem = document.getElementById("__debug_kit");
if (elem) {
    window.__debug_kit_base_url = elem.getAttribute("data-url");
    baseUrl = elem.getAttribute("data-full-url");
    elem = null;
}

$(document).ready(function() {
    toolbar = new Toolbar({
        button: $('#toolbar'),
        content: $('#panel-content-container'),
        panelButtons: $('.panel'),
        panelClose: $('#panel-close'),
        keyboardScope : $(document),
        currentRequest: '<?= $toolbar->id ?>',
        originalRequest: '<?= $toolbar->id ?>',
        baseUrl: __debug_kit_base_url
    });

    toolbar.initialize();

});

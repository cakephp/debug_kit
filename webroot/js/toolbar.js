var __debugKitId,
  __debugKitBaseUrl;
var elem = document.getElementById('__debug_kit');
if (elem) {
  __debugKitId = elem.getAttribute('data-id');
  __debugKitBaseUrl = elem.getAttribute('data-url');
  elem = null;
}

(function(win, doc) {
  var iframe;
  var bodyOverflow;

  var onMessage = function(event) {
    if (event.data === 'collapse') {
      iframe.height = 40;
      iframe.width = 40;
      doc.body.style.overflow = bodyOverflow;
      return;
    }
    if (event.data === 'toolbar') {
      iframe.height = 40;
      iframe.width = '100%';
      doc.body.style.overflow = bodyOverflow;
      return;
    }
    if (event.data === 'expand') {
      iframe.width = '100%';
      iframe.height = '100%';
      doc.body.style.overflow = 'hidden';
      return;
    }
  };

  var onReady = function() {
    if (!win.__debugKitId) {
      return;
    }
    var body = doc.body;

    // Cannot use css text, because of CSP compatibility.
    iframe = doc.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.bottom = 0;
    iframe.style.right = 0;
    iframe.style.border = 0;
    iframe.style.outline = 0;
    iframe.style.overflow = 'hidden';
    iframe.style.zIndex = 99999;
    iframe.height = 40;
    iframe.width = 40;
    iframe.src = __debugKitBaseUrl + 'debug-kit/toolbar/' + __debugKitId;

    body.appendChild(iframe);
    bodyOverflow = body.style.overflow;

    window.addEventListener('message', onMessage, false);
  };

  var logAjaxRequest = function(original) {
    return function() {
      if (this.readyState === 4 && this.getResponseHeader('X-DEBUGKIT-ID')) {
        var params = {
          requestId: this.getResponseHeader('X-DEBUGKIT-ID'),
          status: this.status,
          date: new Date,
          method: this._arguments && this._arguments[0],
          url: this._arguments && this._arguments[1],
          type: this.getResponseHeader('Content-Type')
        };
        iframe.contentWindow.postMessage('ajax-completed$$' + JSON.stringify(params), window.location.origin);
      }
      if (original) {
        return original.apply(this, [].slice.call(arguments));
      }
    };
  };

  var proxyAjaxOpen = function() {
    var proxied = window.XMLHttpRequest.prototype.open;
    window.XMLHttpRequest.prototype.open = function() {
      this._arguments = arguments;
      return proxied.apply(this, [].slice.call(arguments));
    };
  };

  var proxyAjaxSend = function() {
    var proxied = window.XMLHttpRequest.prototype.send;
    window.XMLHttpRequest.prototype.send = function() {
      this.onreadystatechange = logAjaxRequest(this.onreadystatechange);
      return proxied.apply(this, [].slice.call(arguments));
    };
  };

  // Bind on ready callbacks to DOMContentLoaded (native js) and turbolinks:load
  // Turbolinks replaces the body and merges the head of an HTML page.
  // Since the body is already loaded (DOMContentLoaded), the event is not triggered.
  if (doc.addEventListener) {
    // This ensures that all event listeners get applied only once.
    if (!window.__debugKitListenersApplied) {
      // The DOMContentLoaded is for all pages that do not have Turbolinks
      doc.addEventListener('DOMContentLoaded', onReady, false);
      doc.addEventListener('DOMContentLoaded', proxyAjaxOpen, false);
      doc.addEventListener('DOMContentLoaded', proxyAjaxSend, false);

      // turbolinks:load is the alternative DOMContentLoaded Event of Turbolinks
      // https://github.com/turbolinks/turbolinks
      // https://github.com/cakephp/debug_kit/pull/664
      doc.addEventListener('turbolinks:load', onReady, false);
      doc.addEventListener('turbolinks:load', proxyAjaxOpen, false);
      doc.addEventListener('turbolinks:load', proxyAjaxSend, false);
      window.__debugKitListenersApplied = true;
    }
  } else {
    throw new Error('Unable to add event listener for DebugKit. Please use a browser' +
      'that supports addEventListener().');
  }
}(window, document));

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

  // Bind on ready callback.
  if (doc.addEventListener) {
    doc.addEventListener('DOMContentLoaded', onReady, false);
    doc.addEventListener('DOMContentLoaded', proxyAjaxOpen, false);
    doc.addEventListener('DOMContentLoaded', proxyAjaxSend, false);
  } else {
    throw new Error('Unable to add event listener for DebugKit. Please use a browser' +
      'that supports addEventListener().');
  }
}(window, document));

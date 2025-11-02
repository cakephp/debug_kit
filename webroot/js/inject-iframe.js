let elem = document.getElementById('__debug_kit_script');
if (elem) {
  window.debugKitId = elem.getAttribute('data-id');
  window.debugKitBaseUrl = elem.getAttribute('data-url');
  elem = null;
}

((win, doc) => {
  let iframe;
  let bodyOverflow;

  const onMessage = (event) => {
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
    }
    if (event.data === 'error') {
      iframe.width = '40%';
      iframe.height = '40%';
      doc.body.style.overflow = bodyOverflow;
    }
  };

  const onReady = () => {
    if (!win.debugKitId) {
      return;
    }
    const { body } = doc;

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
    iframe.src = `${window.debugKitBaseUrl}debug-kit/toolbar/${window.debugKitId}`;

    body.appendChild(iframe);
    bodyOverflow = body.style.overflow;

    window.addEventListener('message', onMessage, false);
  };

  const logAjaxRequest = (original) => function ajaxRequest() {
    if (this.readyState === 4 && this.getResponseHeader('X-DEBUGKIT-ID')) {
      const params = {
        requestId: this.getResponseHeader('X-DEBUGKIT-ID'),
        status: this.status,
        date: new Date(),
        method: this._arguments && this._arguments[0],
        url: this._arguments && this._arguments[1],
        type: this.getResponseHeader('Content-Type'),
      };
      iframe.contentWindow.postMessage(`ajax-completed$$${JSON.stringify(params)}`, window.location.origin);
    }
    if (original) {
      return original.apply(this, [].slice.call(arguments));
    }
    return false;
  };

  const proxyAjaxOpen = () => {
    const proxied = window.XMLHttpRequest.prototype.open;
    window.XMLHttpRequest.prototype.open = function ajaxCall(...args) {
      this._arguments = args;
      return proxied.apply(this, [].slice.call(args));
    };
  };

  const proxyAjaxSend = () => {
    const proxied = window.XMLHttpRequest.prototype.send;
    window.XMLHttpRequest.prototype.send = function ajaxCall(...args) {
      this.onreadystatechange = logAjaxRequest(this.onreadystatechange);
      return proxied.apply(this, [].slice.call(args));
    };
  };

  const proxyFetch = () => {
    if (!window.fetch) return; // in case browser doesn’t support fetch
    const proxied = window.fetch;

    window.fetch = async function (...args) {
      const [input, init = {}] = args;
      const method = (init.method || 'GET').toUpperCase();
      const url = typeof input === 'string' ? input : input.url;

      let response;
      try {
        response = await proxied.apply(this, args);

        const debugId = response.headers.get('X-DEBUGKIT-ID');
        if (debugId) {
          const params = {
            requestId: debugId,
            status: response.status,
            date: new Date(),
            method,
            url,
            type: response.headers.get('Content-Type'),
          };
          iframe.contentWindow.postMessage(`ajax-completed$$${JSON.stringify(params)}`, window.location.origin);
        }
        return response;
      } catch (error) {
        // still notify iframe of a failed fetch if needed
        const params = {
          requestId: null,
          status: 0,
          date: new Date(),
          method,
          url,
          type: null,
          error: error.message,
        };
        iframe.contentWindow.postMessage(`ajax-completed$$${JSON.stringify(params)}`, window.location.origin);
        throw error;
      }
    };
  };

  // Bind on ready callbacks to DOMContentLoaded (native js)
  // Since the body is already loaded (DOMContentLoaded), the event is not triggered.
  if (doc.addEventListener) {
    // This ensures that all event listeners get applied only once.
    if (!win.debugKitListenersApplied) {
      // Add support for turbo DOMContentLoaded alternative
      // see https://turbo.hotwired.dev/reference/events#turbo%3Aload
      const loadedEvent = typeof Turbo !== 'undefined' && Turbo !== null ? 'turbo:load' : 'DOMContentLoaded';
      doc.addEventListener(loadedEvent, onReady, false);
      doc.addEventListener(loadedEvent, proxyAjaxOpen, false);
      doc.addEventListener(loadedEvent, proxyAjaxSend, false);
      doc.addEventListener(loadedEvent, proxyFetch, false);
      win.debugKitListenersApplied = true;
    }
  } else {
    throw new Error('Unable to add event listener for DebugKit. Please use a browser'
            + ' that supports addEventListener().');
  }
})(window, document);

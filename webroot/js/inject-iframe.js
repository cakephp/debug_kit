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
    const actions = {
      collapse: { height: 40, width: 40, overflow: bodyOverflow },
      toolbar: { height: 40, width: '100%', overflow: bodyOverflow },
      expand: { height: '100%', width: '100%', overflow: 'hidden' },
      error: { height: '40%', width: '40%', overflow: bodyOverflow }
    };

    const action = actions[event.data];
    if (action) {
      iframe.height = action.height;
      iframe.width = action.width;
      doc.body.style.overflow = action.overflow;
    }
  };

  const onReady = () => {
    if (!win.debugKitId) {
      return;
    }

    // Store body overflow before creating iframe
    bodyOverflow = doc.body.style.overflow;

    // Create and configure iframe in one go
    iframe = Object.assign(doc.createElement('iframe'), {
      src: `${window.debugKitBaseUrl}debug-kit/toolbar/${window.debugKitId}`,
      height: 40,
      width: 40
    });

    // Set styles efficiently
    Object.assign(iframe.style, {
      position: 'fixed',
      bottom: 0,
      right: 0,
      border: 0,
      outline: 0,
      overflow: 'hidden',
      zIndex: 99999
    });

    doc.documentElement.appendChild(iframe);
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
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage(`ajax-completed$$${JSON.stringify(params)}`, window.location.origin);
      } else {
        console.error('DebugKit: iframe not available for XHR logging');
      }
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
      win.debugKitListenersApplied = true;
    }
  } else {
    throw new Error('Unable to add event listener for DebugKit. Please use a browser'
            + ' that supports addEventListener().');
  }
})(window, document);

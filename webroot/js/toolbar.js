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
		if (!win.__debug_kit_id) {
			return;
		}
		var body = doc.body;
		iframe = doc.createElement('iframe');
		iframe.setAttribute('style', 'position: fixed; bottom: 0; right: 0; border: 0; outline: 0; overflow: hidden; z-index: 99999;');
		iframe.height = 40;
		iframe.width = 40;
		iframe.src = __debug_kit_base_url + 'debug_kit/toolbar/' + __debug_kit_id;

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
					method: this._arguments[0],
					url: this._arguments[1],
					type: this.getResponseHeader('Content-Type')
				}
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
			'that supports addEventListener().')
	}
}(window, document));

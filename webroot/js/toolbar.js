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

	// Bind on ready callback.
	if (doc.addEventListener) {
		doc.addEventListener('DOMContentLoaded', onReady, false);
	} else {
		throw new Error('Unable to add event listener for DebugKit. Please use a browser' +
			'that supports addEventListener().')
	}
}(window, document));

function Toolbar(options) {
	this.button = options.button;
	this.panelButtons = options.panelButtons;
	this.content = options.content;
	this.panelClose = options.panelClose;
}

Toolbar.prototype = {
	_currentPanel: null,
	_state: 0,

	states: [
		'collapse',
		'toolbar',
	],

	toggle: function() {
		var state = this.nextState();
		this.updateButtons(state);
		window.parent.postMessage(state, window.location.origin)
	},

	state: function() {
		return this.states[this._state];
	},

	nextState: function() {
		this._state++;
		if (this._state == this.states.length) {
			this._state = 0;
		}
		this.saveState();
		return this.state();
	},

	saveState: function() {
		if (!window.localStorage) {
			return;
		}
		window.localStorage.setItem('toolbar_state', this._state);
	},

	loadState: function() {
		if (!window.localStorage) {
			return;
		}
		var old = window.localStorage.getItem('toolbar_state');
		if (!old) {
			old = 0;
		}
		if (old == 0) {
			return this.hideContent();
		}
		if (old == 1) {
			return this.toggle();
		}
	},

	hideButtons: function() {
		this.panelButtons.hide();
	},

	updateButtons: function(state) {
		if (state === 'toolbar') {
			$('.panel').show();
		}
		if (state === 'collapse') {
			$('.panel').hide();
		}
	},

	isExpanded: function() {
		return this.content.is(':visible');
	},

	hideContent: function() {
		this.content.hide();
		this._currentPanel = null;
		window.parent.postMessage(this.state(), window.location.origin);
	},

	loadPanel: function(id) {
		var url = baseUrl + 'debug_kit/panels/view/' + id;
		var contentArea = this.content.find('#panel-content');
		var _this = this;
		this._currentPanel = id;

		// Temporary text.
		contentArea.html('Loading..');

		this.content.show();
		window.parent.postMessage('expand', window.location.origin);

		$.get(url, function(response) {
			contentArea.html(response);
			_this.bindNeatArray();
		});
	},

	bindNeatArray: function() {
		var lists = this.content.find('.depth-0');
		lists.find('ul').hide()
			.parent().addClass('expandable collapsed');

		lists.on('click', 'li', function (event) {
			event.stopPropagation();
			var el = $(this);
			el.children('ul').toggle();
			el.toggleClass('expanded')
				.toggleClass('collapsed');
		});
	},

	currentPanel: function() {
		return this._currentPanel;
	}
};

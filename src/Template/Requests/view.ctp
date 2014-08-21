<?php
use Cake\Routing\Router;
?>

<div id="panel-content-container">
	<span id="panel-close" class="button-close">&times;</span>
	<div id="panel-content">
		<!-- content here -->
	</div>
</div>

<ul class="toolbar">
	<li id="panel-button">DK</li>
	<?php foreach ($toolbar->panels as $panel): ?>
	<li class="panel" data-id="<?= $panel->id ?>">
		<span class="panel-title">
			<?= h($panel->title); ?>
		</span>
	</li>
	<?php endforeach; ?>
</ul>
<?php $this->start('scripts') ?>
<script>
var baseUrl = "<?= Router::url('/', true); ?>";

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
		return this.state();
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
			$(this).children('ul').toggle().toggleClass('expanded collapsed');
		});
	},

	currentPanel: function() {
		return this._currentPanel;
	}
}


$(document).ready(function() {
	var toolbar = new Toolbar({
		button: $('#panel-button'),
		content: $('#panel-content-container'),
		panelButtons: $('.panel'),
		panelClose: $('#panel-close')
	});

	toolbar.button.on('click', function(e) {
		toolbar.toggle();
	});

	toolbar.panelButtons.on('click', function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		var samePanel = toolbar.currentPanel() === id;

		if (toolbar.isExpanded() && samePanel) {
			toolbar.hideContent();
		}
		if (samePanel) {
			return false;
		}
		toolbar.loadPanel(id);
	});

	toolbar.panelClose.on('click', function(e) {
		toolbar.hideContent();
		return false;
	});

	// Start off collapsed.
	toolbar.hideButtons();
});
</script>
<?php $this->end() ?>

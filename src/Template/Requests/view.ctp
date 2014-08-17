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
	<li id="panel-button">Bug</li>
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
	_state: 0,

	states: [
		'collapse',
		'toolbar',
	],

	toggle: function() {
		this.updateButtons(this.nextState());
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
	},

	loadPanel: function(id) {
		var url = baseUrl + 'debug_kit/panels/view/' + id;
		var contentArea = this.content.find('#panel-content');

		// Temporary text.
		contentArea.html('Loading..');

		this.content.show();
		window.parent.postMessage('expand', window.location.origin);

		$.get(url, function(response) {
			contentArea.html(response);
		});
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
		window.parent.postMessage(toolbar.state(), window.location.origin)
	});

	toolbar.panelButtons.on('click', function(e) {
		if (toolbar.isExpanded()) {
			toolbar.hideContent();
			return false;
		}
		toolbar.loadPanel($(this).data('id'));
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

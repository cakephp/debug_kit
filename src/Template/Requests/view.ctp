<?php
use Cake\Routing\Router;
?>

<div id="panel-content">
<!-- content here -->
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

	updateButtons: function(state) {
		if (state === 'toolbar') {
			$('.panel').show();
		}
		if (state === 'collapse') {
			$('.panel').hide();
		}
	}
}

$(document).ready(function() {
	var toolbar = new Toolbar({
		button: $('#panel-button')
	});

	toolbar.button.on('click', function(e) {
		toolbar.toggle();
		window.parent.postMessage(toolbar.state(), window.location.origin)
	});

	var contentArea = $('#panel-content');
	$('.panel').on('click', function(e) {
		if (contentArea.is(':visible')) {
			contentArea.hide();
			return false;
		}
		var panel = $(this);
		var id = panel.data('id');
		var url = baseUrl + 'debug_kit/panels/view/' + id;

		// Temporary text.
		contentArea.html('Loading..');
		contentArea.show();
		window.parent.postMessage('expand', window.location.origin);

		$.get(url, function(response) {
			// contentArea.html(response);
		});
	});

	// Start off collapsed.
	$('.panel').hide();
});
</script>
<?php $this->end() ?>

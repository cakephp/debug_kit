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

<div class="panel-content">
<!-- content here -->
</div>

<?php $this->start('scripts') ?>
<script>

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

	// Start off collapsed.
	$('.panel').hide();
});
</script>
<?php $this->end() ?>

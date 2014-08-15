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
function nextState() {
	var states = [
		'collapse',
		'toolbar',
		'expand'
	];
	if (this.state === undefined) {
		this.state = 0;
	}
	this.state++;
	if (this.state > states.length) {
		this.state = 0;
	}
	return states[this.state];
}

function updateToolbar(state) {
	if (state === 'toolbar') {
		$('.panel').show();
	}
	if (state === 'collapse') {
		$('.panel').hide();
	}
}

$(document).ready(function() {
	$('#panel-button').on('click', function(e) {
		var state = nextState();
		updateToolbar(state);
		window.parent.postMessage(state, window.location.domain)
	});

	// Start off collapsed.
	$('.panel').hide();
});
</script>
<?php $this->end() ?>

<div class="toolbar">
<ul>
	<li id="panel-button">Bug</li>
	<?php foreach ($toolbar->panels as $panel): ?>
	<li class="panel" data-id="<?= $panel->id ?>">
		<span class="panel-title">
			<?= h($panel->title); ?>
		</span>
		<div class="panel-region">
		<!-- content here -->
		</div>
	</li>
	<?php endforeach; ?>
</ul>
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


$(document).ready(function() {
	$('#panel-button').on('click', function(e) {
		window.parent.postMessage(nextState(), window.location.domain)
	});

	// Start off collapsed.
	$('.panel').hide();
});
</script>
<?php $this->end() ?>

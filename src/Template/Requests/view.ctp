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
	<li id="panel-button">
		<?= $this->Html->image('DebugKit.cake.icon.png', ['alt' => 'Debug Kit']) ?>
	</li>
	<?php foreach ($toolbar->panels as $panel): ?>
	<li class="panel" data-id="<?= $panel->id ?>">
		<span class="panel-button">
			<?= h($panel->title); ?>
		</span>
	</li>
	<?php endforeach; ?>
</ul>
<?php $this->start('scripts') ?>
<script>
var baseUrl = "<?= Router::url('/', true); ?>";

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

	toolbar.loadState();
});
</script>
<?php $this->end() ?>

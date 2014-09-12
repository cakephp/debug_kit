<?php
use Cake\Routing\Router;
?>
<div id="panel-content-container">
	<span id="panel-close" class="button-close">&times;</span>
	<div id="panel-content">
		<!-- content here -->
	</div>
</div>

<ul id="toolbar" class="toolbar">
	<?php foreach ($toolbar->panels as $panel): ?>
	<li class="panel" data-id="<?= $panel->id ?>" style="display: none;">
		<span class="panel-button">
			<?= h($panel->title) ?>
		</span>
		<?php if (strlen($panel->summary)): ?>
		<span class="panel-summary">
			<?= h($panel->summary) ?>
		</span>
		<?php endif ?>
	</li>
	<?php endforeach; ?>
	<li id="panel-button">
		<?= $this->Html->image('DebugKit.cake.icon.png', ['alt' => 'Debug Kit']) ?>
	</li>
</ul>
<?php $this->start('scripts') ?>
<script>
var baseUrl = "<?= Router::url('/', true) ?>";
var toolbar;

$(document).ready(function() {
	toolbar = new Toolbar({
		button: $('#toolbar'),
		content: $('#panel-content-container'),
		panelButtons: $('.panel'),
		panelClose: $('#panel-close'),
		keyboardScope : $(document),
		currentRequest: '<?= $toolbar->id ?>',
		originalRequest: '<?= $toolbar->id ?>'
	});

	toolbar.keyboardListener();

	toolbar.button.on('click', function(e) {
		toolbar.toggle();
	});

	toolbar.panelButtons.on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
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

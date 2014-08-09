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

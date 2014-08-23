<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 1.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Routing\Router;
?>
<?php if (empty($requests)): ?>
	<p class="warning"><?= __d('debug_kit', 'No previous requests logged.'); ?></p>
<?php else: ?>
	<?= count($requests); ?> <?= __d('debug_kit', 'previous requests available') ?>
	<ul class="history-list">
		<li>
			<?= $this->Html->link(
				__d('debug_kit', 'Current request'),
				['plugin' => 'DebugKit', 'controller' => 'Requests', 'action' => 'view', $panel->request_id],
				['class' => 'active history-link']
			); ?>
		</li>
		<?php foreach ($requests as $request): ?>
			<?php $url = ['plugin' => 'DebugKit', 'controller' => 'Panels', 'action' => 'index', $request->id] ?>
			<li>
				<?= $this->Html->link($request->url, $url, ['class' => 'history-link']); ?>
				<span class="history-time"><?= h($request->requested_at) ?></span>
				<span class="history-code"><?= h($request->status_code) ?></span>
				<span class="history-type"><?= h($request->content_type) ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<script>
$(document).ready(function() {
	var panelButtons = $('.panel');
	var thisPanel = '<?= h($panel->id) ?>';
	var urlBase = '<?= Router::fullBaseUrl() ?>';

	$('.history-link').on('click', function(e) {
		var el = $(this);
		e.preventDefault();
		el.addClass('active');

		$.getJSON(el.attr('href'), function(response) {
			for (var i = 0, len = response.panels.length; i < len; i++) {
				var panel = response.panels[i];
				var button = panelButtons.eq(i);

				// Don't overwrite the history panel.
				if (button.data('id') === thisPanel) {
					continue;
				}
				button.data('id', panel.id);
			}
		});
	});
});
</script>

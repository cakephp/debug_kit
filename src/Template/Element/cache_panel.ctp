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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<?php if (empty($metrics)): ?>
	<p class="info"><?php echo __d('debug_kit', 'There were no cache operations this request.'); ?></p>
<?php else: ?>
	<?php foreach ($metrics as $name => $counters): ?>
		<h3><?= __d('debug_kit', '{0} Metrics', h($name)) ?> </h3>
		<table cellspacing="0" cellpadding="0" class="debug-table">
			<thead>
				<tr><th><?= __d('debug_kit', 'Metric') ?></th><th><?= __d('debug_kit', 'Total') ?></th></tr>
			</thead>
			<tbody>
			<?php foreach ($counters as $key => $val): ?>
				<tr>
				<td><?= h($key) ?></td>
				<td class="right-text"><?= $val ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>
<?php endif; ?>

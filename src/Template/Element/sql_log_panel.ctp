<?php
/**
 * SQL Log Panel Element
 *
 * PHP 5
 *
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

$headers = array('Query', 'Num. rows', 'Took (ms)');
if (isset($debugKitInHistoryMode)) {
	$content = $this->Toolbar->readCache('sql_log', $this->request->params['pass'][0]);
}
?>
<h2><?php echo __d('debug_kit', 'Sql Logs')?></h2>
<?php if (!empty($content)) : ?>
	<?php foreach ($content['loggers'] as $logger): ?>
	<div class="sql-log-panel-query-log">
		<h4><?= h($logger->name()) ?></h4>
		<?php
			$queries = $logger->queries();
			if (empty($queries)):
				echo ' ' . __d('debug_kit', 'No query logs.');
			else:
				echo '<h5>';
				echo __d(
					'debug_kit',
					'Total Time: %s ms &mdash; Total Queries: %s &mdash; Total Rows: %s',
					$logger->totalTime(),
					count($queries),
					$logger->totalRows()
				);
				echo '</h5>';
				echo $this->Toolbar->table($queries, $headers, array('title' => 'SQL Log ' . $logger->name()));
			?>
		<h4><?php echo __d('debug_kit', 'Query Explain:'); ?></h4>
		<div id="sql-log-explain-<?= h($logger->name()); ?>">
			<a id="debug-kit-explain-<?= h($logger->name()); ?>"> </a>
			<p><?php echo __d('debug_kit', 'Click an "Explain" link above, to see the query explanation.'); ?></p>
		</div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
<?php else:
	echo $this->Toolbar->message('Warning', __d('debug_kit', 'No active database connections'));
endif;

<?php
/**
 * Log Panel Element
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<h2><?php echo __d('debug_kit', 'Logs') ?></h2>
<div class="code-table">
<?php if ($content instanceof DebugKitLogListener): ?>
	<?php foreach ($content->logs as $logName => $logs): ?>
		<h3><?php echo $logName ?></h3>
		<?php
			$len = count($logs);
			if ($len > 0):
				$headers = array(__d('debug_kit', 'Time'), __d('debug_kit', 'Message'));
				$rows = array();
				for ($i = 0; $i < $len; $i++):
					$rows[] = array(
						$logs[$i][0], h($logs[$i][1])
					);
				endfor;
				echo $this->Toolbar->table($rows, $headers, array('title' => $logName));
			endif; ?>
	<?php endforeach; ?>
	<?php if (empty($content->logs)): ?>
		<p class="info"><?php echo __d('debug_kit', 'There were no log entries made this request'); ?></p>
	<?php endif; ?>
<?php endif; ?>
</div>

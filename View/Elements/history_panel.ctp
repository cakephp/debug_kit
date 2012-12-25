<?php
/**
 * View Variables Panel Element
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
 * @since         DebugKit 1.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<h2> <?php echo __d('debug_kit', 'Request History'); ?></h2>
<?php if (empty($content)): ?>
	<p class="warning"><?php echo __d('debug_kit', 'No previous requests logged.'); ?></p>
<?php else: ?>
	<?php echo count($content); ?> <?php echo __d('debug_kit', 'previous requests available') ?>
	<ul class="history-list">
		<li><?php echo $this->Html->link(__d('debug_kit', 'Restore to current request'),
			'#', array('class' => 'history-link', 'id' => 'history-restore-current')); ?>
		</li>
		<?php foreach ($content as $previous): ?>
			<li><?php echo $this->Html->link($previous['title'], $previous['url'], array('class' => 'history-link')); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
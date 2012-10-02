<?php
/**
 * Debug Toolbar Element
 *
 * Renders all of the other panel elements.
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
<div id="debug-kit-toolbar">
	<?php if (empty($debugToolbarPanels)) :?>
		<p class="warning"><?php echo __d('debug_kit', 'There are no active panels. You must enable a panel to see its output.'); ?></p>
	<?php else: ?>
		<?php echo $this->Html->image('/debug_kit/img/cake.icon.png', array('alt' => 'CakePHP', 'class' => 'icon')); ?>
		<ul id="panel-tabs">
		<?php
			// Decrescent order, because of float right
			uasort($debugToolbarPanels, function ($a, $b) {
				if ($a['priority'] == $b['priority']) {
					return 0;
				}
				return ($a['priority'] < $b['priority']) ? -1 : 1;
			});

			foreach ($debugToolbarPanels as $panelName => $panelInfo): ?>
			<?php $panelUnderscore = Inflector::underscore($panelName);?>
			<li class="panel-tab<?php if ($panelInfo['priority'] > 0) { echo " featured";} ?>">
			<?php

				$title = (empty($panelInfo['title'])) ? Inflector::humanize($panelUnderscore) : $panelInfo['title'];
				$this->element($panelInfo['elementName'], array_merge($panelInfo, array('title' => $title)), array(
					'plugin' => (empty($panelInfo['plugin'])) ? null : Inflector::camelize($panelInfo['plugin'])
				));

				echo $this->Toolbar->panelStart(($this->fetch('panelTitle') ? $this->fetch('panelTitle') : $title), $panelUnderscore);
			?>
				<div class="panel-content" id="<?php echo $panelUnderscore ?>-tab">
					<a href="#" class="panel-toggle ui-control ui-button">+</a>
					<div class="panel-resize-region">
						<div class="panel-content-data">
							<?php echo $this->fetch('panelContent'); ?>
						</div>
						<div class="panel-content-data panel-history" id="<?php echo $panelUnderscore; ?>-history">
							<!-- content here -->
						</div>
					</div>
					<div class="panel-resize-handle ui-control">====</div>
				</div>
			<?php $this->Toolbar->panelEnd(); ?>
			</li>
		<?php
			$this->assign('panelTitle', '');
			$this->assign('panelContent', '');
		endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
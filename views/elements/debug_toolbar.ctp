<?php
/**
 * Debug Toolbar Element
 *
 * Renders all of the other panel elements.
 *
 * PHP versions 4 and 5
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
		<p class="warning"><?php __d('debug_kit', 'There are no active panels. You must enable a panel to see its output.'); ?></p>
	<?php else: ?>
		<ul id="panel-tabs">
			<li class="panel-tab icon">
				<a href="#hide" id="hide-toolbar">
					<?php echo $html->image('/debug_kit/img/cake.icon.png', array('alt' => 'CakePHP')); ?>
				</a>
			</li>
		<?php foreach ($debugToolbarPanels as $panelName => $panelInfo): ?>
			<?php $panelUnderscore = Inflector::underscore($panelName);?>
			<li class="panel-tab">
			<?php
				$title = (empty($panelInfo['title'])) ? Inflector::humanize($panelUnderscore) : $panelInfo['title'];
				echo $toolbar->panelStart($title, $panelUnderscore);
			?>
				<div class="panel-content" id="<?php echo $panelUnderscore ?>-tab">
					<a href="#" class="panel-maximize ui-control ui-button">+</a>
					<a href="#" class="panel-minimize ui-control ui-button">–</a>
					<div class="panel-resize-region">
						<div class="panel-content-data">
							<?php echo $this->element($panelInfo['elementName'], $panelInfo); ?>
						</div>
						<div class="panel-content-data panel-history" id="<?php echo $panelUnderscore; ?>-history">
							<!-- content here -->
						</div>
					</div>
					<div class="panel-resize-handle ui-control">====</div>
				</div>
			<?php $toolbar->panelEnd(); ?>
			</li>
		<?php endforeach ?>
		</ul>
	<?php endif; ?>
</div>
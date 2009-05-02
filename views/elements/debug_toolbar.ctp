<?php
/**
 * Debug Toolbar Element
 *
 * Renders all of the other panel elements.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<div id="debug-kit-toolbar">
	<?php if (empty($debugToolbarPanels)) :?>
		<p class="warning"><?php __('There are no active panels. You must enable a panel to see its output.'); ?></p>
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
				<a href="#<?php echo $panelUnderscore; ?>">
					<?php echo Inflector::humanize($panelUnderscore); ?>
				</a>
				<div class="panel-content" id="<?php echo $panelUnderscore ?>-tab">
					<div class="panel-content-data">
						<?php echo $this->element($panelInfo['elementName'], $panelInfo); ?>
					</div>
					<div class="panel-content-data panel-content-history" id="<?php echo $panelUnderscore; ?>-history">
						<!-- content here -->
					</div>
				</div>
			</li>
		<?php endforeach ?>
		</ul>
	<?php endif; ?>
</div>
<?php
/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Element
 *
 * Renders all of the other panel elements.
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.debug_kit.views.elements
 * @since         
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<div id="debug-kit-toolbar">
	<?php if (empty($debugToolbarPanels)) :?>
		<p class="warning"><?php __('There are no active panels. You must enable a panel to see its output.'); ?></p>
	<?php else: ?>
		<ul id="panel-tabs">
			<li class="panel-tab icon">
				<a href="#hide" id="hide-toolbar">
					<?php echo $html->image('/debug_kit/img/cake.icon.png', array('alt' => 'cakePHP')); ?>
				</a>
			</li>
			<?php foreach ($debugToolbarPanels as $panelName => $panelInfo): ?>
				<li class="panel-tab">
					<a href="#<?php echo Inflector::underscore($panelName); ?>">
						<?php echo Inflector::humanize(Inflector::underscore($panelName)); ?>
					</a>
					<div class="panel-content" id="<?php echo Inflector::underscore($panelName); ?>-tab">
						<?php echo $this->element($panelInfo['elementName'], $panelInfo); ?>
					</div>
				</li>
			<?php endforeach ?>
		</ul>
	<?php endif; ?>
</div>
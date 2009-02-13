<?php
/* SVN FILE: $Id$ */
/**
 * View Variables Panel Element
 *
 *
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
<h2> <?php __('Request History'); ?></h2>
<?php if (empty($content)): ?>
  <p class="warning"><?php __('No previous requests logged.'); ?></p>
<?php else: ?>
	<?php echo count($content); ?> <?php __('previous requests available') ?>
	<ul class="history-list">
		<li><?php echo $html->link(__('Restore to current request', true), 
			'#', array('class' => 'history-link', 'id' => 'history-restore-current')); ?>
		</li>
		<?php foreach ($content as $previous): ?>
			<li><?php echo $html->link($previous['title'], $previous['url'], array('class' => 'history-link')); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

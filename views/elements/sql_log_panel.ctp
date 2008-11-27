<?php
/* SVN FILE: $Id$ */
/**
 * Session Panel Element
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
<h2><?php __('Sql Logs')?></h2>
<?php if (!empty($content)) : ?>
	<?php foreach ($content as $dbName => $queryLog) : ?>
	<div class="sql-log-panel-query-log">
		<h4><?php echo $dbName ?></h4>
		<?php echo $queryLog; ?>
	</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="warning"><?php __('No active database connections'); ?>
<?php endif; ?>
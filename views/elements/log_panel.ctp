<?php
/* SVN FILE: $Id$ */
/**
 * Log Panel Element
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
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<h2><?php __('Logs') ?></h2>
<div class="code-table">
<?php foreach ($content as $logName => $logs): ?>
	<h3><?php echo $logName ?></h3>
	<?php 
		$len = count($logs);
		if ($len > 0):
			$headers = array(__('Time', true), __('Message', true));
			$rows = array();
			for ($i = 0; $i < $len; $i += 2):
				$rows[] = array(
					$logs[$i], $logs[$i + 1]
				);
			endfor;
			echo $toolbar->table($rows, $headers, array('title' => $logName));
		else: ?>
		<p class="info"><?php __('There were no log entries made this request'); ?></p>
	<?php endif; ?>
<?php endforeach; ?>
</div>
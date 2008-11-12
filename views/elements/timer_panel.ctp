<?php
/* SVN FILE: $Id$ */
/**
 * Timer Panel Element
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
 * @subpackage    cake.cake.libs.
 * @since         CakePHP v 1.2.0.4487
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$timers = DebugKitDebugger::getTimers();
?>
<h2><?php __('Timers'); ?></h2>
<p class="request-time">
	<strong><?php __('Total Request Time:') ?></strong>
	<?php echo sprintf(__('%s (seconds)', true), $number->precision(DebugKitDebugger::requestTime(), 6)); ?>
</p>

<table class="debug-table">
	<thead>
		<tr>
			<th>Message</th>
			<th>time in seconds</th>
		</tr>
	</thead>
	<tbody>
	<?php $i = 0; ?>
	<?php foreach ($timers as $timerName => $timeInfo): ?>
		<tr class="<?php echo ($i % 2) ? 'even' : 'odd'; ?>">
			<td><?php echo $timeInfo['message']?></td>
			<td><?php echo $number->precision($timeInfo['time'], 6); ?> </td>
		</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
	</tbody>
</table>

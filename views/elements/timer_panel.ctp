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
 * @subpackage    cake.debug_kit.views.elements
 * @since         
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$timers = DebugKitDebugger::getTimers();
?>
<h2><?php __('Timers'); ?></h2>
<p class="request-time">
	<?php $totalTime = sprintf(__('%s (seconds)', true), $number->precision(DebugKitDebugger::requestTime(), 6)); ?>
	<?php echo $toolbar->message(__('Total Request Time:', true), $totalTime)?>
</p>

<?php foreach ($timers as $timerName => $timeInfo):
	$rows[] = array(
		$timeInfo['message'],
		$number->precision($timeInfo['time'], 6)
	);
	$headers = array(__('Message', true), __('time in seconds', true));
endforeach;
echo $toolbar->table($rows, $headers, array('title' => 'Timers')); ?>
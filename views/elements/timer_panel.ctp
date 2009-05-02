<?php
/**
 * Timer Panel Element
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
if (!isset($debugKitInHistoryMode)):
	$timers = DebugKitDebugger::getTimers();
	$currentMemory = DebugKitDebugger::getMemoryUse();
	$peakMemory = DebugKitDebugger::getPeakMemoryUse();
	$requestTime = DebugKitDebugger::requestTime();	
else:
	$content = $toolbar->readCache('timer', $this->params['pass'][0]);
	if (is_array($content)):
		extract($content);
	endif;
endif;
?>
<h2><?php __('Memory'); ?></h2>
<div class="current-mem-use">
	<?php echo $toolbar->message(__('Current Memory Use',true), $number->toReadableSize($currentMemory)); ?>
</div>
<div class="peak-mem-use">
<?php
	echo $toolbar->message(__('Peak Memory Use', true), $number->toReadableSize($peakMemory)); ?>
</div>

<h2><?php __('Timers'); ?></h2>
<div class="request-time">
	<?php $totalTime = sprintf(__('%s (seconds)', true), $number->precision($requestTime, 6)); ?>
	<?php echo $toolbar->message(__('Total Request Time:', true), $totalTime)?>
</div>

<?php
$maxTime = 0;
foreach ($timers as $timerName => $timeInfo):
	$maxTime += $timeInfo['time'];
endforeach;

$headers = array(__('Message', true), __('Time in seconds', true), __('Graph', true));

foreach ($timers as $timerName => $timeInfo):
	$rows[] = array(
		$timeInfo['message'],
		$number->precision($timeInfo['time'], 6),
		$simpleGraph->bar($number->precision($timeInfo['time'], 6), array('max' => $maxTime))
	);
endforeach;

echo $toolbar->table($rows, $headers, array('title' => 'Timers')); 

if (!isset($debugKitInHistoryMode)):
	$toolbar->writeCache('timer', compact('timers', 'currentMemory', 'peakMemory', 'requestTime'));
endif;
?>

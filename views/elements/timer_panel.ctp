<?php
/**
 * Timer Panel Element
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
if (!isset($debugKitInHistoryMode)):
	$timers = DebugKitDebugger::getTimers(true);
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
<div class="debug-info">
	<h2><?php __d('debug_kit', 'Memory'); ?></h2>
	<div class="peak-mem-use">
	<?php
		echo $toolbar->message(__d('debug_kit', 'Peak Memory Use', true), $number->toReadableSize($peakMemory)); ?>
	</div>

	<?php
	$headers = array(__d('debug_kit', 'Message', true), __d('debug_kit', 'Memory use', true));
	$memoryPoints = DebugKitDebugger::getMemoryPoints();

	$rows = array();
	foreach($memoryPoints as $key => $value):
		$rows[] = array($key, $number->toReadableSize($value));
	endforeach;

	echo $toolbar->table($rows, $headers);
	?>
</div>

<div class="debug-info debug-timers">
	<h2><?php __d('debug_kit', 'Timers'); ?></h2>
	<div class="request-time">
		<?php $totalTime = sprintf(__d('debug_kit', '%s (ms)', true), $number->precision($requestTime * 1000, 0)); ?>
		<?php echo $toolbar->message(__d('debug_kit', 'Total Request Time:', true), $totalTime)?>
	</div>
<?php
$rows = array();
$end = end($timers);
$maxTime = $end['end'];

$headers = array(
	__d('debug_kit', 'Message', true),
	__d('debug_kit', 'Time in ms', true),
	__d('debug_kit', 'Graph', true)
);

$i = 0;
$values = array_values($timers);

foreach ($timers as $timerName => $timeInfo):
	$indent = 0;
	for ($j = 0; $j < $i; $j++) {
		if (($values[$j]['end'] > $timeInfo['start']) && ($values[$j]['end']) > ($timeInfo['end'])) {
			$indent++;
		}
	}
	$indent = str_repeat(' » ', $indent);
	$rows[] = array(
		$indent . $timeInfo['message'],
		$number->precision($timeInfo['time'] * 1000, 2),
		$simpleGraph->bar(
			$number->precision($timeInfo['time'] * 1000, 2),
			$number->precision($timeInfo['start'] * 1000, 2),
			array(
				'max' => $maxTime * 1000,
				'requestTime' => $requestTime * 1000,
			)
		)
	);
	$i++;
endforeach;

if (strtolower($toolbar->getName()) == 'firephptoolbar'):
	for ($i = 0, $len = count($rows); $i < $len; $i++):
		unset($rows[$i][2]);
	endfor;
	unset($headers[2]);
endif;

echo $toolbar->table($rows, $headers, array('title' => 'Timers'));

if (!isset($debugKitInHistoryMode)):
	$toolbar->writeCache('timer', compact('timers', 'currentMemory', 'peakMemory', 'requestTime'));
endif;
?>
</div>
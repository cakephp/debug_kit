<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$this->addHelper('Number');
$this->addHelper('DebugKit.SimpleGraph');
?>
<section>
	<h3><?php echo __d('debug_kit', 'Memory'); ?></h3>
	<div class="peak-mem-use">
		<strong><?= __d('debug_kit', 'Peak Memory Use:') ?></strong>
		<?= $this->Number->toReadableSize($peakMemory); ?>
	</div>

	<?php
	$headers = array(__d('debug_kit', 'Message'), __d('debug_kit', 'Memory use'));
	$rows = array();
	foreach ($memory as $key => $value):
		$rows[] = array($key, $this->Number->toReadableSize($value));
	endforeach;
	echo $this->Toolbar->table($rows, $headers);
	?>
</div>
</section>

<section>
	<h3><?php echo __d('debug_kit', 'Timers'); ?></h3>
	<div class="request-time">
		<strong><?= __d('debug_kit', 'Total Request Time:') ?></strong>
		<?= $this->Number->precision($requestTime * 1000, 0) ?> ms
	</div>
<?php
$rows = array();
$end = end($timers);
$maxTime = $end['end'];

$headers = array(
	__d('debug_kit', 'Message'),
	__d('debug_kit', 'Time in ms'),
	__d('debug_kit', 'Graph')
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
	$indent = str_repeat(' &raquo; ', $indent);
	$rows[] = array(
		$indent . $timeInfo['message'],
		$this->Number->precision($timeInfo['time'] * 1000, 2),
		$this->SimpleGraph->bar(
			$this->Number->precision($timeInfo['time'] * 1000, 2),
			$this->Number->precision($timeInfo['start'] * 1000, 2),
			array(
				'max' => $maxTime * 1000,
				'requestTime' => $requestTime * 1000,
			)
		)
	);
	$i++;
endforeach;

echo $this->Toolbar->table($rows, $headers);
?>
</section>

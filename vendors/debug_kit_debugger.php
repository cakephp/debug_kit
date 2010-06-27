<?php
/**
 * DebugKit Debugger class. Extends and enhances core
 * debugger. Adds benchmarking and timing functionality.
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
 * @subpackage    debug_kit.vendors
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Core', 'Debugger');
App::import('Vendor', 'DebugKit.FireCake');

/**
 * Debug Kit Temporary Debugger Class
 *
 * Provides the future features that are planned. Yet not implemented in the 1.2 code base
 *
 * This file will not be needed in future version of CakePHP.
 */
class DebugKitDebugger extends Debugger {
/**
 * Internal benchmarks array
 *
 * @var array
 **/
	var $__benchmarks = array();
/**
 * Internal memory points array
 *
 * @var array
 **/
	var $__memoryPoints = array();
/**
 * destruct method
 *
 * Allow timer info to be displayed if the code dies or is being debugged before rendering the view
 * Cheat and use the debug log class for formatting
 *
 * @return void
 * @access private
 */
	function __destruct() {
		$_this =& DebugKitDebugger::getInstance();
		if (Configure::read('debug') < 2 || !$_this->__benchmarks) {
			return;
		}
		$timers = array_values(DebugKitDebugger::getTimers());
		$end = end($timers);
		echo '<table class="cake-sql-log"><tbody>';
		echo '<caption>Debug timer info</caption>';
		echo '<tr><th>Message</th><th>Start Time (ms)</th><th>End Time (ms)</th><th>Duration (ms)</th></tr>';
		$i = 0;
		foreach ($timers as $timer) {
			$indent = 0;
			for ($j = 0; $j < $i; $j++) {
				if (($timers[$j]['end']) > ($timer['start']) && ($timers[$j]['end']) > ($timer['end'])) {
					$indent++;
				}
			}
			$indent = str_repeat(' » ', $indent);

			extract($timer);
			$start = round($start * 1000, 0);
			$end = round($end * 1000, 0);
			$time = round($time * 1000, 0);
			echo "<tr><td>{$indent}$message</td><td>$start</td><td>$end</td><td>$time</td></tr>";
			$i++;
		}
		echo '</tbody></table>';
	}
/**
 * Start an benchmarking timer.
 *
 * @param string $name The name of the timer to start.
 * @param string $message A message for your timer
 * @return bool true
 * @static
 **/
	function startTimer($name = null, $message = null) {
		$start = getMicrotime();
		$_this =& DebugKitDebugger::getInstance();

		if (!$name) {
			$named = false;
			$calledFrom = debug_backtrace();
			$_name = $name = Debugger::trimpath($calledFrom[0]['file']) . ' line ' . $calledFrom[0]['line'];
		} else {
			$named = true;
		}

		if (!$message) {
			$message = $name;
		}

		$_name = $name;
		$i = 1;
		while (isset($_this->__benchmarks[$name])) {
			$i++;
			$name = $_name . ' #' . $i;
		}

		if ($i > 1) {
			$message .= ' #' . $i;
		}

		$_this->__benchmarks[$name] = array(
			'start' => $start,
			'message' => $message,
			'named' => $named
		);
		return true;
	}
/**
 * Stop a benchmarking timer.
 *
 * $name should be the same as the $name used in startTimer().
 *
 * @param string $name The name of the timer to end.
 * @access public
 * @return boolean true if timer was ended, false if timer was not started.
 * @static
 */
	function stopTimer($name = null) {
		$end = getMicrotime();
		$_this =& DebugKitDebugger::getInstance();
		if (!$name) {
			$names = array_reverse(array_keys($_this->__benchmarks));
			foreach($names as $name) {
				if (!empty($_this->__benchmarks[$name]['end'])) {
					continue;
				}
				if (empty($_this->__benchmarks[$name]['named'])) {
					break;
				}
			}
		} else {
			$i = 1;
			$_name = $name;
			while (isset($_this->__benchmarks[$name])) {
				if (empty($_this->__benchmarks[$name]['end'])) {
					break;
				}
				$i++;
				$name = $_name . ' #' . $i;
			}
		}
		if (!isset($_this->__benchmarks[$name])) {
			return false;
		}
		$_this->__benchmarks[$name]['end'] = $end;
		return true;
	}
/**
 * Get all timers that have been started and stopped.
 * Calculates elapsed time for each timer. If clear is true, will delete existing timers
 *
 * @param bool $clear false
 * @return array
 * @access public
 **/
	function getTimers($clear = false) {
		$_this =& DebugKitDebugger::getInstance();
		$start = DebugKitDebugger::requestStartTime();
		$now = getMicrotime();

		$times = array();
		if (!empty($_this->__benchmarks)) {
			$firstTimer = current($_this->__benchmarks);
			$_end = $firstTimer['start'];
		} else {
			$_end = $now;
		}
		$times['Core Processing (Derived)'] = array(
			'message' => __d('debug_kit', 'Core Processing (Derived)', true),
			'start' => 0,
			'end' => $_end - $start,
			'time' => round($_end - $start, 6),
			'named' => null
		);
		foreach ($_this->__benchmarks as $name => $timer) {
			if (!isset($timer['end'])) {
				$timer['end'] = $now;
			}
			$times[$name] = array_merge($timer, array(
				'start' => $timer['start'] - $start,
				'end' => $timer['end'] - $start,
				'time' => DebugKitDebugger::elapsedTime($name)
			));
		}
		if ($clear) {
			$_this->__benchmarks = array();
		}
		return $times;
	}
/**
 * Clear all existing timers
 *
 * @return bool true
 **/
	function clearTimers() {
		$_this =& DebugKitDebugger::getInstance();
		$_this->__benchmarks = array();
		return true;
	}
/**
 * Get the difference in time between the timer start and timer end.
 *
 * @param $name string the name of the timer you want elapsed time for.
 * @param $precision int the number of decimal places to return, defaults to 5.
 * @return float number of seconds elapsed for timer name, 0 on missing key
 * @static
 **/
	function elapsedTime($name = 'default', $precision = 5) {
		$_this =& DebugKitDebugger::getInstance();
		if (!isset($_this->__benchmarks[$name]['start']) || !isset($_this->__benchmarks[$name]['end'])) {
			return 0;
		}
		return round($_this->__benchmarks[$name]['end'] - $_this->__benchmarks[$name]['start'], $precision);
	}
/**
 * Get the total execution time until this point
 *
 * @access public
 * @return float elapsed time in seconds since script start.
 * @static
 */
	function requestTime() {
		$start = DebugKitDebugger::requestStartTime();
		$now = getMicroTime();
		return ($now - $start);
	}
/**
 * get the time the current request started.
 *
 * @access public
 * @return float time of request start
 * @static
 */
	function requestStartTime() {
		if (defined('TIME_START')) {
			$startTime = TIME_START;
		} else if (isset($GLOBALS['TIME_START'])) {
			$startTime = $GLOBALS['TIME_START'];
		} else {
			$startTime = env('REQUEST_TIME');
		}
		return $startTime;
	}
/**
 * get current memory usage
 *
 * @return integer number of bytes ram currently in use. 0 if memory_get_usage() is not available.
 * @static
 **/
	function getMemoryUse() {
		if (!function_exists('memory_get_usage')) {
			return 0;
		}
		return memory_get_usage();
	}
/**
 * Get peak memory use
 *
 * @return integer peak memory use (in bytes).  Returns 0 if memory_get_peak_usage() is not available
 * @static
 **/
	function getPeakMemoryUse() {
		if (!function_exists('memory_get_peak_usage')) {
			return 0;
		}
		return memory_get_peak_usage();
	}
/**
 * Stores a memory point in the internal tracker.
 * Takes a optional message name which can be used to identify the memory point.
 * If no message is supplied a debug_backtrace will be done to identifty the memory point.
 * If you don't have memory_get_xx methods this will not work.
 *
 * @param string $message Message to identify this memory point.
 * @return boolean
 **/
	function setMemoryPoint($message = null) {
		$memoryUse = DebugKitDebugger::getMemoryUse();
		if (!$message) {
			$named = false;
			$trace = debug_backtrace();
			$message = Debugger::trimpath($trace[0]['file']) . ' line ' . $trace[0]['line'];
		}
		$self =& DebugKitDebugger::getInstance();
		if (isset($self->__memoryPoints[$message])) {
			$originalMessage = $message;
			$i = 1;
			while (isset($self->__memoryPoints[$message])) {
				$i++;
				$message = $originalMessage . ' #' . $i;
			}
		}
		$self->__memoryPoints[$message] = $memoryUse;
		return true;
	}
/**
 * Get all the stored memory points
 *
 * @param boolean $clear Whether you want to clear the memory points as well. Defaults to false.
 * @return array Array of memory marks stored so far.
 **/
	function getMemoryPoints($clear = false) {
		$self =& DebugKitDebugger::getInstance();
		$marks = $self->__memoryPoints;
		if ($clear) {
			$self->__memoryPoints = array();
		}
		return $marks;
	}
/**
 * Clear out any existing memory points
 *
 * @return void
 **/
	function clearMemoryPoints() {
		$self =& DebugKitDebugger::getInstance();
		$self->__memoryPoints = array();
	}
/**
 * Handles object conversion to debug string.
 *
 * @param string $var Object to convert
 * @access protected
 */
	function _output($data = array()) {
		extract($data);
		if (is_array($level)) {
			$error = $level['error'];
			$code = $level['code'];
			if (isset($level['helpID'])) {
				$helpID = $level['helpID'];
			} else {
				$helpID = '';
			}
			$description = $level['description'];
			$file = $level['file'];
			$line = $level['line'];
			$context = $level['context'];
			$level = $level['level'];
		}
		$files = $this->trace(array('start' => 2, 'format' => 'points'));
		$listing = $this->excerpt($files[0]['file'], $files[0]['line'] - 1, 1);
		$trace = $this->trace(array('start' => 2, 'depth' => '20'));

		if ($this->_outputFormat == 'fb') {
			$kontext = array();
			foreach ((array)$context as $var => $value) {
				$kontext[] = "\${$var}\t=\t" . $this->exportVar($value, 1);
			}
			$this->_fireError($error, $code, $description, $file, $line, $trace, $kontext);
		} else {
			$data = compact(
				'level', 'error', 'code', 'helpID', 'description', 'file', 'path', 'line', 'context'
			);
			echo parent::_output($data);
		}
	}
/**
 * Create a FirePHP error message
 *
 * @param string $error Name of error
 * @param string $code  Code of error
 * @param string $description Description of error
 * @param string $file File error occured in
 * @param string $line Line error occured on
 * @param string $trace Stack trace at time of error
 * @param string $context context of error
 * @return void
 * @access protected
 */
	function _fireError($error, $code, $description, $file, $line, $trace, $context) {
		$name = $error . ' - ' . $description;
		$message = "$error $code $description on line: $line in file: $file";
		FireCake::group($name);
		FireCake::error($message, $name);
		FireCake::log($context, 'Context');
		FireCake::log($trace, 'Trace');
		FireCake::groupEnd();
	}
}


Debugger::invoke(DebugKitDebugger::getInstance('DebugKitDebugger'));
Debugger::getInstance('DebugKitDebugger');

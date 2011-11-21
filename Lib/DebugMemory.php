<?php
/**
 * Contains methods for Profiling memory usage.
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.Lib
 * @since         DebugKit 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Debugger', 'Utility');

class DebugMemory {

/**
 * An array of recorded memory use points.
 *
 * @var array
 */
	private static $__points = array();

/**
 * Get current memory usage
 *
 * @return integer number of bytes ram currently in use. 0 if memory_get_usage() is not available.
 */
	public static function getCurrent() {
		return memory_get_usage();
	}

/**
 * Get peak memory use
 *
 * @return integer peak memory use (in bytes).  Returns 0 if memory_get_peak_usage() is not available
 */
	public static function getPeak() {
		return memory_get_peak_usage();
	}

/**
 * Stores a memory point in the internal tracker.
 * Takes a optional message name which can be used to identify the memory point.
 * If no message is supplied a debug_backtrace will be done to identifty the memory point.
 *
 * @param string $message Message to identify this memory point.
 * @return boolean
 */
	public static function record($message = null) {
		$memoryUse = self::getCurrent();
		if (!$message) {
			$named = false;
			$trace = debug_backtrace();
			$message = Debugger::trimpath($trace[0]['file']) . ' line ' . $trace[0]['line'];
		}
		if (isset(self::$__points[$message])) {
			$originalMessage = $message;
			$i = 1;
			while (isset(self::$__points[$message])) {
				$i++;
				$message = $originalMessage . ' #' . $i;
			}
		}
		self::$__points[$message] = $memoryUse;
		return true;
	}

/**
 * Get all the stored memory points
 *
 * @param boolean $clear Whether you want to clear the memory points as well. Defaults to false.
 * @return array Array of memory marks stored so far.
 */
	public static function getAll($clear = false) {
		$marks = self::$__points;
		if ($clear) {
			self::$__points = array();
		}
		return $marks;
	}
/**
 * Clear out any existing memory points
 *
 * @return void
 */
	public static function clear() {
		self::$__points = array();
	}

}

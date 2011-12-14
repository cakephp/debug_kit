<?php
/**
 * Benchmark Shell.
 *
 * Provides basic benchmarking of application requests
 * functionally similar to Apache AB
 *
 * PHP versions 5
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
 * @subpackage    debug_kit.vendors.shells
 * @since         DebugKit 1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/

/**
 * Benchmark Shell Class
 *
 * @package cake
 * @subpackage cake.debug_kit.vendors.shells
 * @todo Print/export time detail information
 * @todo Export/graphing of data to .dot format for graphviz visualization
 * @todo Make calculated results round to leading significant digit position of std dev.
 */
App::uses('String','Utility');

class BenchmarkShell extends Shell {
/**
 * Main execution of shell
 *
 * @return void
 */
	public function main() {
		if (empty($this->args) || count($this->args) > 1) {
			return $this->help();
		}

		$url = $this->args[0];
		$defaults = array('t' => 100, 'n' => 10);
		$options  = array_merge($defaults, $this->params);
		$times = array();

		$this->out(String::insert(__d('debug_kit', '-> Testing :url'), compact('url')));
		$this->out("");
		for ($i = 0; $i < $options['n']; $i++) {
			if (floor($options['t'] - array_sum($times)) <= 0 || $options['n'] <= 1) {
				break;
			}

			$start = microtime(true);
			file_get_contents($url);
			$stop = microtime(true);

			$times[] = $stop - $start;
		}
		$this->_results($times);
	}
/**
 * Prints calculated results
 *
 * @param array $times Array of time values
 * @return void
 */
	protected function _results($times) {
		$duration = array_sum($times);
		$requests = count($times);

		$this->out(String::insert(__d('debug_kit', 'Total Requests made: :requests'), compact('requests')));
		$this->out(String::insert(__d('debug_kit', 'Total Time elapsed: :duration (seconds)'), compact('duration')));

		$this->out("");

		$this->out(String::insert(__d('debug_kit', 'Requests/Second: :rps req/sec'), array(
				'rps' => round($requests / $duration, 3)
		)));

		$this->out(String::insert(__d('debug_kit', 'Average request time: :average-time seconds'), array(
				'average-time' => round($duration / $requests, 3)
		)));

		$this->out(String::insert(__d('debug_kit', 'Standard deviation of average request time: :std-dev'), array(
				'std-dev' => round($this->_deviation($times, true), 3)
		)));

		$this->out(String::insert(__d('debug_kit', 'Longest/shortest request: :longest sec/:shortest sec'), array(
				'longest' => round(max($times), 3),
				'shortest' => round(min($times), 3)
		)));

		$this->out("");

	}
/**
 * One-pass, numerically stable calculation of population variance.
 *
 * Donald E. Knuth (1998).
 * The Art of Computer Programming, volume 2: Seminumerical Algorithms, 3rd edn.,
 * p. 232. Boston: Addison-Wesley.
 *
 * @param array $times Array of values
 * @param boolean $sample If true, calculates an unbiased estimate of the population
 * 						  variance from a finite sample.
 * @return float Variance
 */
	protected function _variance($times, $sample = true) {
		$n = $mean = $M2 = 0;

		foreach($times as $time){
			$n += 1;
			$delta = $time - $mean;
			$mean = $mean + $delta/$n;
			$M2 = $M2 + $delta*($time - $mean);
		}

		if ($sample) $n -= 1;

		return $M2/$n;
	}
/**
 * Calculate the standard deviation.
 *
 * @param array $times Array of values
 * @return float Standard deviation
 */
	protected function _deviation($times, $sample = true) {
		return sqrt($this->_variance($times, $sample));
	}
/**
 * Help for Benchmark shell
 *
 * @return void
 */
	public function help() {
		$this->out(__d('debug_kit', "DebugKit Benchmark Shell"));
		$this->out("");
		$this->out(__d('debug_kit', "\tAllows you to obtain some rough benchmarking statistics \n\tabout a fully qualified URL."));
		$this->out("");
		$this->out(__d('debug_kit', "\tUse:"));
		$this->out(__d('debug_kit', "\t\tcake benchmark [-n iterations] [-t timeout] url"));
		$this->out("");
		$this->out(__d('debug_kit', "\tParams:"));
		$this->out(__d('debug_kit', "\t\t-n Number of iterations to perform. Defaults to 10. \n\t\t   Must be an integer."));
		$this->out(__d('debug_kit', "\t\t-t Maximum total time for all iterations, in seconds. \n\t\t   Defaults to 100. Must be an integer."));
		$this->out("");
		$this->out(__d('debug_kit', "\tIf a single iteration takes more than the \n\ttimeout specified, only one request will be made."));
		$this->out("");
		$this->out(__d('debug_kit', "\tExample Use:"));
		$this->out(__d('debug_kit', "\t\tcake benchmark -n 10 -t 100 http://localhost/testsite"));
		$this->out("");
		$this->out(__d('debug_kit', "\tNote that this benchmark does not include browser render time"));
		$this->out("");
		$this->hr();
		$this->out("");
	}
}


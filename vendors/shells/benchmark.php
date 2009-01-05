<?php
/* SVN FILE: $Id$ */
/**
 * Benchmark Shell.
 *
 * Provides basic benchmarking of application requests 
 * functionally similar to Apache AB
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
 * @subpackage    cake.debug_kit.vendors.shells
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class BenchmarkShell extends Shell {
	function main() {
		$url = $this->args[0];
		$this->out(sprintf('-> Testing %s', $url));

		$n = 100;
		$t = $i = 0;
		if (isset($this->params['n'])) {
			$n = $this->params['n'];
		}
		if (isset($this->params['t'])) {
			$t = $this->params['t'];
		}
		$start = microtime(true);
		while (true) {
			if ($t && $t <= floor(microtime(true) - $start)) {
				break;
			}
			if ($n <= $i) {
				break;
			}
			file_get_contents($url);
			$i++;
		}
		$duration = microtime(true) - $start;
		$this->out('Total Requests made: ' . $i);
		$this->out('Total Time elapsed: ' . $duration . '(s)');
		$this->out(round($n / $duration, 2).' req/sec');
	}
	
	function help() {
		$out = <<<EOL
	DebugKit Benchmark Shell
	
	Test a fully qualified url to get avg requests per second.
	By default it does 100 requests to the provided url.
	
	Use:
		cake benchmark [params] [url]
	
	Params
		-n The maximum number of iterations to do.
		
		-t The maximum time to take. If a single request takes more
			than this time. Only one request will be made.
EOL;
		$this->out($out);
	}
}

?>
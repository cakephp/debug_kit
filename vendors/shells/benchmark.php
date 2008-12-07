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
		$start = microtime(true);
		for ($i = 0; $i < $n; $i++) {
			file_get_contents($url);
		}
		$duration = microtime(true) - $start;
		$this->out(round($n / $duration, 2).' req/sec');
	}
}

?>
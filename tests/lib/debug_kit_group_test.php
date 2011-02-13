<?php

abstract class DebugkitGroupTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	abstract public static function suite();

	protected static function _getSuite($label = null) {

		if ($label === null) {

			if (version_compare(PHP_VERSION, '5.3.0', '<')) {
				$trace = debug_backtrace(false);
				$class = $trace[1]['class'];
			} else {
				$class = get_called_class();
			}

			$label = Inflector::humanize(Inflector::underscore($class));

		}

		$suite = new PHPUnit_Framework_TestSuite($label);
		return $suite;

	}

	protected static function _testFiles($directory = null, $excludes = null) {

		if (is_array($directory)) {
			$files = array();
			foreach ($directory as $d) {
				$files = array_merge($files, self::_testFiles($d, $excludes));
			}
			return array_unique($files);
		}

		if ($excludes !== null) {
			$excludes = self::_testFiles((array)$excludes);
		}
		if ($directory === null || $directory !== realpath($directory)) {

			$basePath = App::pluginPath('DebugKit') . 'tests' . DS . 'cases' . DS;
			$directory = str_replace(DS . DS, DS, $basePath . $directory);

		}

		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

		$files = array();
		while ($it->valid()) {

			if (!$it->isDot()) {
				$file = $it->key();

				if (
					preg_match('|\.test\.php$|', $file) &&
					$file !== __FILE__ &&
					!preg_match('|all_|', $file) &&
					($excludes === null || !in_array($file, $excludes))
				) {

					$files[] = $file;
				}
			}

			$it->next();
		}

		return $files;

	}

}
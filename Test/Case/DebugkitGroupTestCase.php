<?php

class DebugkitGroupTestCase extends PHPUnit_Framework_TestSuite {

	public function __construct() {
		$label = Inflector::humanize(Inflector::underscore(get_class($this)));
		parent::__construct($label);
	}

	public static function getTestFiles($directory = null, $excludes = null) {

		if (is_array($directory)) {
			$files = array();
			foreach ($directory as $d) {
				$files = array_merge($files, self::getTestFiles($d, $excludes));
			}
			return array_unique($files);
		}

		if ($excludes !== null) {
			$excludes = self::getTestFiles((array)$excludes);
		}
		if ($directory === null || $directory !== realpath($directory)) {

			$basePath = App::pluginPath('DebugKit') . 'Test' . DS . 'Case' . DS;
			$directory = str_replace(DS . DS, DS, $basePath . $directory);

		}

		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

		$files = array();
		while ($it->valid()) {

			if (!$it->isDot()) {
				$file = $it->key();

				if (
					preg_match('|Test\.php$|', $file) &&
					$file !== __FILE__ &&
					!preg_match('|^All.+?\.php$|', basename($file)) &&
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

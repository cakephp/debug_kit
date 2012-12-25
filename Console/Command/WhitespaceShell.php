<?php
/**
 * Whitespace shell. Helps find and trim whitespace from files.
 *
 * Based on jperras' shell found at http://bin.cakephp.org/view/626544881
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
 * @since         DebugKit 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Folder', 'Utility');

class WhitespaceShell extends Shell {

/**
 * Will check files for whitespace and notify you
 * of any files containing leading or trailing whitespace.
 *
 * @return void
 */
	public function main() {
		$path = APP;
		if (!empty($this->params['path']) && strpos($this->params['path'], '/') === 0) {
			$path = $this->params['path'];
		} elseif (!empty($this->params['path'])) {
			$path .= $this->params['path'];
		}
		$folder = new Folder($path);

		$r = $folder->findRecursive('.*\.php');
		$this->out("Checking *.php in " . $path);
		foreach ($r as $file) {
			$c = file_get_contents($file);
			if (preg_match('/^[\n\r|\n\r|\n|\r|\s]+\<\?php/', $c)) {
				$this->out('!!!contains leading whitespaces: ' . $this->shortPath($file));
			}
			if (preg_match('/\?\>[\n\r|\n\r|\n|\r|\s]+$/', $c)) {
				$this->out('!!!contains trailing whitespaces: ' . $this->shortPath($file));
			}
		}
	}

/**
 * Much like main() except files are modified. Be sure to have
 * backups or use version control.
 *
 * @return void
 */
	public function trim() {
		$path = APP;
		if (!empty($this->params['path']) && strpos($this->params['path'], '/') === 0) {
			$path = $this->params['path'];
		} elseif (!empty($this->params['path'])) {
			$path .= $this->params['path'];
		}
		$folder = new Folder($path);

		$r = $folder->findRecursive('.*\.php');
		$this->out("Checking *.php in " . $path);
		foreach ($r as $file) {
			$c = file_get_contents($file);
			if (preg_match('/^[\n\r|\n\r|\n|\r|\s]+\<\?php/', $c) || preg_match('/\?\>[\n\r|\n\r|\n|\r|\s]+$/', $c)) {
				$this->out('trimming' . $this->shortPath($file));
				$c = preg_replace('/^[\n\r|\n\r|\n|\r|\s]+\<\?php/', '<?php', $c);
				$c = preg_replace('/\?\>[\n\r|\n\r|\n|\r|\s]+$/', '?>', $c);
				file_put_contents($file, $c);
			}
		}
	}

/**
 * get the option parser
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->addOption('path', array('short' => 'p', 'help' => __d('cake_console', 'Absolute path or relative to APP.')));
	}
}

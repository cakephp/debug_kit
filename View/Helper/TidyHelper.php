<?php
App::uses('File', 'Utility');

/**
 * Tidy helper - passes html through tidy on the command line, and reports markup errors
 *
 * PHP version 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       debug_kit
 * @subpackage    debug_kit.views.helpers
 * @since         v 1.0 (22-Jun-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * TidyHelper class
 *
 * @uses          AppHelper
 * @package       debug_kit
 * @subpackage    debug_kit.views.helpers
 */
class TidyHelper extends AppHelper {

/**
 * helpers property
 *
 * @var array
 * @access public
 */
	public $helpers = array('DebugKit.Toolbar');

/**
 * results property
 *
 * @var mixed null
 * @access public
 */
	public $results = null;

/**
 * Return a nested array of errors for the passed html string
 * Fudge the markup slightly so that the tag which is invalid is highlighted
 *
 * @param string $html ''
 * @param string $out ''
 * @return array
 * @access public
 */
	public function process($html = '', &$out = '') {
		$errors = $this->tidyErrors($html, $out);
		
		
		if (!$errors) {
			return;
		}
		$result = array('Error' => array(), 'Warning' => array(), 'Misc' => array());
		$errors = explode("\n", $errors);
		$markup = explode("\n", $out);
		foreach ($errors as $error) {
			preg_match('@line (\d+) column (\d+) - (\w+): (.*)@', $error, $matches);
			if ($matches) {
				list($original, $line, $column, $type, $message) = $matches;
				$line = $line - 1;

				$string = '</strong>';
				if (isset($markup[$line - 1])) {
					$string .= h($markup[$line - 1]);
				}
				$string .= '<strong>' . h(@$markup[$line]) . '</strong>';
				if (isset($markup[$line + 1])) {
					$string .= h($markup[$line + 1]);
				}
				$string .= '</strong>';

				$result[$type][$string][] = h($message);
			} elseif ($error) {
				$message = $error;
				$result['Misc'][h($message)][] = h($message);
			}
		}
		$this->results = $result;
		return $result;
	}

/**
 * report method
 *
 * Call process if a string is passed, or no prior results exist - and return the results using
 * the toolbar helper to generate a nested navigatable array
 *
 * @param mixed $html null
 * @return string
 * @access public
 */
	public function report($html = null) {
		if ($html) {
			$this->process($html);
		} elseif ($this->results === null) {
			$this->process($this->_View->output);
		}
		if (!$this->results) {
			return '<p>' . __d('debug_kit', 'No markup errors found') . '</p>';
		}
		foreach ($this->results as &$results) {
			foreach ($results as $type => &$messages) {
				foreach ($messages as &$message) {
					$message = html_entity_decode($message, ENT_COMPAT, Configure::read('App.encoding'));
				}
			}
		}
		return $this->Toolbar->makeNeatArray(array_filter($this->results), 0, 0, false);
	}

/**
 * Run the html string through tidy, and return the (raw) errors. pass back a reference to the
 * normalized string so that the error messages can be linked to the line that caused them.
 *
 * @param string $in ''
 * @param string $out ''
 * @return string
 * @access public
 */
	public function tidyErrors($in = '', &$out = '') {
		$out = preg_replace('@>\s*<@s', ">\n<", $in);
		
		// direct access? windows etc
		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($out, array(), 'UTF8');
			$tidy->cleanRepair();
			$errors = $tidy->errorBuffer. "\n";
			return $errors;
		}
		
		// cli		
		$File = new File(rtrim(TMP, DS) . DS . rand() . '.html', true);
		$File->write($out);
		$path = $File->pwd();
		$errors = $path . '.err';
		$this->_exec("tidy -eq -utf8 -f $errors $path");
		$File->delete();

		if (!file_exists($errors)) {
			return;
		}
		$Error = new File($errors);
		$errors = $Error->read();
		$Error->delete();
		return $errors;
	}

/**
 * exec method
 *
 * @param mixed $cmd
 * @param mixed $out null
 * @return void
 * @access protected
 */
	protected function _exec($cmd, &$out = null) {
		if (DS === '/') {
			$_out = exec($cmd . ' 2>&1', $out, $return);
		} else {
			$_out = exec($cmd, $out, $return);
		}

		if (Configure::read('debug')) {
			$source = Debugger::trace(array('depth' => 1, 'start' => 2)) . "\n";
			//CakeLog::write('system_calls_' . date('Y-m-d'), "\n" . $source . Debugger::exportVar(compact('cmd','out','return')));
			//CakeLog::write('system_calls', "\n" . $source . Debugger::exportVar(compact('cmd','out','return')));
		}
		if ($return) {
			return false;
		}
		return $_out ? $_out : true;
	}

}
<?php
/**
 * FirePHP Class for CakePHP
 *
 * Provides most of the functionality offered by FirePHPCore
 * Interoperates with FirePHP extension for firefox
 *
 * For more information see: http://www.firephp.org/
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
 * @subpackage    debug_kit.views.helpers
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
App::import('Core', 'Debugger');

if (!function_exists('firecake')) {
	function firecake($message, $label = null) {
		FireCake::fb($message, $label, 'log');
	}
}

class FireCake extends Object {
/**
 * Options for FireCake.
 *
 * @see _defaultOptions and setOptions();
 * @var string
 */
	var	$options = array();
/**
 * Default Options used in CakeFirePhp
 *
 * @var string
 * @access protected
 */
	var $_defaultOptions = array(
		'maxObjectDepth' => 10,
	    'maxArrayDepth' => 20,
	    'useNativeJsonEncode' => true,
	    'includeLineNumbers' => true,
	);
/**
 * Message Levels for messages sent via FirePHP
 *
 * @var array
 */
	var $_levels = array(
		'log' => 'LOG',
		'info' => 'INFO',
		'warn' => 'WARN',
		'error' => 'ERROR',
		'dump' => 'DUMP',
		'trace' => 'TRACE',
		'exception' => 'EXCEPTION',
		'table' => 'TABLE',
		'groupStart' => 'GROUP_START',
		'groupEnd' => 'GROUP_END',
	);

	var $_version = '0.2.1';
/**
 * internal messageIndex counter
 *
 * @var int
 * @access protected
 */
	var $_messageIndex = 1;
/**
 * stack of objects encoded by stringEncode()
 *
 * @var array
 **/
	var $_encodedObjects = array();
/**
 * methodIndex to include in tracebacks when using includeLineNumbers
 *
 * @var array
 **/
	var $_methodIndex = array('info', 'log', 'warn', 'error', 'table', 'trace');
/**
 * FireCake output status
 *
 * @var bool
 **/
	var $_enabled = true;
/**
 * get Instance of the singleton
 *
 * @param string $class Class instance to store in the singleton. Used with subclasses and Tests.
 * @access public
 * @static
 * @return void
 */
	function &getInstance($class = null) {
		static $instance = array();
		if (!empty($class)) {
			if (!$instance || strtolower($class) != strtolower(get_class($instance[0]))) {
				$instance[0] =& new $class();
				$instance[0]->setOptions();
			}
		}
		if (!isset($instance[0]) || !$instance[0]) {
			$instance[0] =& new FireCake();
			$instance[0]->setOptions();
		}
		return $instance[0];
	}
/**
 * setOptions
 *
 * @param array $options Array of options to set.
 * @access public
 * @static
 * @return void
 */
	function setOptions($options = array()) {
		$_this =& FireCake::getInstance();
		if (empty($_this->options)) {
			$_this->options = array_merge($_this->_defaultOptions, $options);
		} else {
			$_this->options = array_merge($_this->options, $options);
		}
	}
/**
 * Return boolean based on presence of FirePHP extension
 *
 * @access public
 * @return boolean
 **/
	function detectClientExtension() {
		$ua = FireCake::getUserAgent();
		if (!preg_match('/\sFirePHP\/([\.|\d]*)\s?/si', $ua, $match) || !version_compare($match[1], '0.0.6', '>=')) {
			return false;
		}
		return true;
	}
/**
 * Get the Current UserAgent
 *
 * @access public
 * @static
 * @return string UserAgent string of active client connection
 **/
	function getUserAgent() {
		return env('HTTP_USER_AGENT');
	}
/**
 * Disable FireCake output
 * All subsequent output calls will not be run.
 *
 * @return void
 **/
	function disable() {
		$_this =& FireCake::getInstance();
		$_this->_enabled = false;
	}
/**
 * Enable FireCake output
 *
 * @return void
 **/
	function enable() {
		$_this =& FireCake::getInstance();
		$_this->_enabled = true;
	}
/**
 * Convenience wrapper for LOG messages
 *
 * @param string $message Message to log
 * @param string $label Label for message (optional)
 * @access public
 * @static
 * @return void
 */
	function log($message, $label = null) {
		FireCake::fb($message, $label, 'log');
	}
/**
 * Convenience wrapper for WARN messages
 *
 * @param string $message Message to log
 * @param string $label Label for message (optional)
 * @access public
 * @static
 * @return void
 */
	function warn($message, $label = null) {
		FireCake::fb($message, $label, 'warn');
	}
/**
 * Convenience wrapper for INFO messages
 *
 * @param string $message Message to log
 * @param string $label Label for message (optional)
 * @access public
 * @static
 * @return void
 */
	function info($message, $label = null) {
		FireCake::fb($message, $label, 'info');
	}
/**
 * Convenience wrapper for ERROR messages
 *
 * @param string $message Message to log
 * @param string $label Label for message (optional)
 * @access public
 * @static
 * @return void
 */
	function error($message, $label = null) {
		FireCake::fb($message, $label, 'error');
	}
/**
 * Convenience wrapper for TABLE messages
 *
 * @param string $message Message to log
 * @param string $label Label for message (optional)
 * @access public
 * @static
 * @return void
 */
	function table($label, $message) {
		FireCake::fb($message, $label, 'table');
	}
/**
 * Convenience wrapper for DUMP messages
 *
 * @param string $message Message to log
 * @param string $label Unique label for message
 * @access public
 * @static
 * @return void
 */
	function dump($label, $message) {
		FireCake::fb($message, $label, 'dump');
	}
/**
 * Convenience wrapper for TRACE messages
 *
 * @param string $label Label for message (optional)
 * @access public
 * @return void
 */
	function trace($label)  {
		FireCake::fb($label, 'trace');
	}
/**
 * Convenience wrapper for GROUP messages
 * Messages following the group call will be nested in a group block
 *
 * @param string $label Label for group (optional)
 * @access public
 * @return void
 */
	function group($label)  {
		FireCake::fb(null, $label, 'groupStart');
	}
/**
 * Convenience wrapper for GROUPEND messages
 * Closes a group block
 *
 * @param string $label Label for group (optional)
 * @access public
 * @return void
 */
	function groupEnd()  {
		FireCake::fb(null, null, 'groupEnd');
	}
/**
 * fb - Send messages with FireCake to FirePHP
 *
 * Much like FirePHP's fb() this method can be called with various parameter counts
 * fb($message) - Just send a message defaults to LOG type
 * fb($message, $type) - Send a message with a specific type
 * fb($message, $label, $type) - Send a message with a custom label and type.
 *
 * @param mixed $message Message to output. For other parameters see usage above.
 * @static
 * @return void
 **/
	function fb($message) {
		$_this =& FireCake::getInstance();

		if (headers_sent($filename, $linenum)) {
			trigger_error(sprintf(__d('debug_kit', 'Headers already sent in %s on line %s. Cannot send log data to FirePHP.', true), $filename, $linenum), E_USER_WARNING);
			return false;
		}
		if (!$_this->_enabled || !$_this->detectClientExtension()) {
			return false;
		}

		$args = func_get_args();
		$type = $label = null;
		switch (count($args)) {
			case 1:
				$type = $_this->_levels['log'];
				break;
			case 2:
				$type = $args[1];
				break;
			case 3:
				$type = $args[2];
				$label = $args[1];
				break;
			default:
				trigger_error(__d('debug_kit', 'Incorrect parameter count for FireCake::fb()', true), E_USER_WARNING);
				return false;
		}
		if (isset($_this->_levels[$type])) {
			$type = $_this->_levels[$type];
		} else {
			$type = $_this->_levels['log'];
		}

		$meta = array();
		$skipFinalObjectEncode = false;
		if ($type == $_this->_levels['trace']) {
			$trace = debug_backtrace();
			if (!$trace) {
				return false;
			}
			$message = $_this->_parseTrace($trace, $args[0]);
			$skipFinalObjectEncode = true;
		}

		if ($_this->options['includeLineNumbers']) {
			if (!isset($meta['file']) || !isset($meta['line'])) {
				$trace = debug_backtrace();
				for ($i = 0, $len = count($trace); $i < $len ; $i++) {
					$keySet = (isset($trace[$i]['class']) && isset($trace[$i]['function']));
					$selfCall = ($keySet && 
						strtolower($trace[$i]['class']) == 'firecake' &&
						in_array($trace[$i]['function'], $_this->_methodIndex)
					);
					if ($selfCall) {
						$meta['File'] = isset($trace[$i]['file']) ? Debugger::trimPath($trace[$i]['file']) : '';
						$meta['Line'] = isset($trace[$i]['line']) ? $trace[$i]['line'] : '';
						break;
					}
				}
			}
		}

		$structureIndex = 1;
		if ($type == $_this->_levels['dump']) {
			$structureIndex = 2;
			$_this->_sendHeader('X-Wf-1-Structure-2','http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
		} else {
			$_this->_sendHeader('X-Wf-1-Structure-1','http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
		}

		$_this->_sendHeader('X-Wf-Protocol-1', 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
		$_this->_sendHeader('X-Wf-1-Plugin-1', 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/'. $_this->_version);
		if ($type == $_this->_levels['groupStart']) {
			$meta['Collapsed'] = 'true';
		}
		if ($type == $_this->_levels['dump']) {
			$dump = $_this->jsonEncode($message);
			$msg = '{"' . $label .'":' . $dump .'}';
		} else {
			$meta['Type'] = $type;
			if ($label !== null) {
				$meta['Label'] = $label;
			}
			$msg = '[' . $_this->jsonEncode($meta) . ',' . $_this->jsonEncode($message, $skipFinalObjectEncode).']';
		}

		$lines = explode("\n", chunk_split($msg, 5000, "\n"));

		foreach ($lines as $i => $line) {
			if (empty($line)) {
				continue;
			}
			$header = 'X-Wf-1-' . $structureIndex . '-1-' . $_this->_messageIndex;
			if (count($lines) > 2) {
				$first = ($i == 0) ? strlen($msg) : '';
				$end = ($i < count($lines) - 2) ? '\\' : '';
				$message = $first . '|' . $line . '|' . $end;
				$_this->_sendHeader($header, $message);
			} else {
				$_this->_sendHeader($header, strlen($line) . '|' . $line . '|');
			}
			$_this->_messageIndex++;
			if ($_this->_messageIndex > 99999) {
				trigger_error(__d('debug_kit', 'Maximum number (99,999) of messages reached!', true), E_USER_WARNING);
			}
		}
		$_this->_sendHeader('X-Wf-1-Index', $_this->_messageIndex - 1);
		return true;
	}
/**
 * Parse a debug backtrace
 *
 * @param array $trace Debug backtrace output
 * @access protected
 * @return array
 **/
	function _parseTrace($trace, $messageName) {
		$message = array();
		for ($i = 0, $len = count($trace); $i < $len ; $i++) {
			$keySet = (isset($trace[$i]['class']) && isset($trace[$i]['function']));
			$selfCall = ($keySet && $trace[$i]['class'] == 'FireCake');
			if (!$selfCall) {
				$message = array(
					'Class' => isset($trace[$i]['class']) ? $trace[$i]['class'] : '',
					'Type' => isset($trace[$i]['type']) ? $trace[$i]['type'] : '',
					'Function' => isset($trace[$i]['function']) ? $trace[$i]['function'] : '',
					'Message' => $messageName,
					'File' => isset($trace[$i]['file']) ? Debugger::trimPath($trace[$i]['file']) : '',
					'Line' => isset($trace[$i]['line']) ? $trace[$i]['line'] : '',
					'Args' => isset($trace[$i]['args']) ? $this->stringEncode($trace[$i]['args']) : '',
					'Trace' => $this->_escapeTrace(array_splice($trace, $i + 1))
				);
				break;
			}
		}
		return $message;
	}
/**
 * Fix a trace for use in output
 *
 * @param mixed $trace Trace to fix
 * @access protected
 * @static
 * @return string
 **/
	function _escapeTrace($trace) {
		for ($i = 0, $len = count($trace); $i < $len; $i++) {
			if (isset($trace[$i]['file'])) {
				$trace[$i]['file'] = Debugger::trimPath($trace[$i]['file']);
			}
			if (isset($trace[$i]['args'])) {
				$trace[$i]['args'] = $this->stringEncode($trace[$i]['args']);
			}
		}
		return $trace;
	}
/**
 * Encode non string objects to string.
 * Filter out recursion, so no errors are raised by json_encode or $javascript->object()
 *
 * @param mixed $object Object or variable to encode to string.
 * @param int $objectDepth Current Depth in object chains.
 * @param int $arrayDepth Current Depth in array chains.
 * @static
 * @return void
 **/
	function stringEncode($object, $objectDepth = 1, $arrayDepth = 1) {
		$_this =& FireCake::getInstance();
		$return = array();
		if (is_resource($object)) {
			return '** ' . (string)$object . '**';
		}
		if (is_object($object)) {
			if ($objectDepth == $_this->options['maxObjectDepth']) {
				return '** Max Object Depth (' . $_this->options['maxObjectDepth'] . ') **';
			}
			foreach ($_this->_encodedObjects as $encoded) {
				if ($encoded === $object) {
					return '** Recursion (' . get_class($object) . ') **';
				}
			}
			$_this->_encodedObjects[] =& $object;

			$return['__className'] = $class = get_class($object);
			$properties = (array)$object;
			foreach ($properties as $name => $property) {
				$return[$name] = FireCake::stringEncode($property, 1, $objectDepth + 1);
			}
			array_pop($_this->_encodedObjects);
		}
		if (is_array($object)) {
			if ($arrayDepth == $_this->options['maxArrayDepth']) {
				return '** Max Array Depth ('. $_this->options['maxArrayDepth'] . ') **';
			}
			foreach ($object as $key => $value) {
				$return[$key] = FireCake::stringEncode($value, 1, $arrayDepth + 1);
			}
		}
		if (is_string($object) || is_numeric($object) || is_bool($object) || is_null($object)) {
			return $object;
		}
		return $return;
	}
/**
 * Encode an object into JSON
 *
 * @param mixed $object Object or array to json encode
 * @param boolean $doIt
 * @access public
 * @static
 * @return string
 **/
	function jsonEncode($object, $skipEncode = false) {
		$_this =& FireCake::getInstance();
		if (!$skipEncode) {
			$object = FireCake::stringEncode($object);
		}

		if (function_exists('json_encode') && $_this->options['useNativeJsonEncode']) {
			return json_encode($object);
		} else {
			return FireCake::_jsonEncode($object);
		}
	}
/**
 * jsonEncode Helper method for PHP4 compatibility
 *
 * @param mixed $object Something to encode
 * @access protected
 * @static
 * @return string
 **/
	function _jsonEncode($object) {
		if (!class_exists('JavascriptHelper')) {
			App::import('Helper', 'Javascript');
		}
		$javascript =& new JavascriptHelper();
		$javascript->useNative = false;
		if (is_string($object)) {
			return '"' . $javascript->escapeString($object) . '"';
		}
		return $javascript->object($object);
	}
/**
 * Send Headers - write headers.
 *
 * @access protected
 * @return void
 **/
	function _sendHeader($name, $value) {
		header($name . ': ' . $value);
	}
}

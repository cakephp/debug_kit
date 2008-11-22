<?php
/* SVN FILE: $Id$ */
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
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright       Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link            http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package         cake
 * @subpackage      cake.cake.libs.
 * @since           CakePHP v 1.2.0.4487
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
 */
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
 * get Instance of the singleton
 *
 * @access public
 * @return void
 */
	function &getInstance() {
		static $instance = array();
		if (!isset($instance[0]) || !$instance[0]) {
			$args = func_get_args();
			if (!isset($args[0])) {
				$args[0] = 'FireCake';
			}
			$instance[0] = new $args[0]();
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
		$_this = FireCake::getInstance();
		if (empty($_this->options)) {
			$_this->options = array_merge($_this->_defaultOptions, $options);
		} else {
			$_this->options = array_merge($_this->options, $options);
		}
	}
	
	function log($message) {
		FireCake::fb($message, 'log');
	}
	
	function warn($message) {
		FireCake::fb($message, 'warn');
	}
	
	function info($message) {
		FireCake::fb($message, 'info');
	}
	
	function error($message) {
		FireCake::fb($message, 'error');
	}
	
	function table($message) {
		FireCake::fb($message, 'table');
	}
	
	function dump($message) {
		FireCake::fb($message, 'dump');
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
 * @return void
 **/
	function fb($message) {
		$_this = FireCake::getInstance();

		if (headers_sent($filename, $linenum)) {
			trigger_error(sprintf(__('Headers already sent in %s on line %s. Cannot send log data to FirePHP.', true),$filename, $linenum), E_USER_WARNING);
			return false;
		}
		if (!$_this->detectClientExtension()) {
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
				trigger_error(__('Incorrect parameter count for FireCake::fb()', true), E_USER_WARNING);
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
			//handle traces
			
			$skipFinalObjectEncode = true;
		}
		if ($type == $_this->_levels['table']) {
			//handle tables
			
			$skipFinalObjectEncode = true;
		}

		if ($_this->options['includeLineNumbers']) {
			//handle line numbers
		}
		$structureIndex = 1;
		if ($type == $_this->_levels['dump']) {
			$structureIndex = 2;
			$_this->_sendHeader('X-Wf-1-Structure-2','http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
		} else {
			$_this->_sendHeader('X-Wf-1-Structure-1','http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
		}

		$_this->_sendHeader('X-Wf-Protocol-1','http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
		$_this->_sendHeader('X-Wf-1-Plugin-1','http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/'. $_this->_version);
		
		if ($type == $_this->_levels['dump']) {
			//handle dump
		} else {
			$metaMsg = array('Type' => $type);
			if ($label !== null) {
				$metaMsg['Label'] = $label;
			}
			if (isset($meta['file'])) {
				$metaMsg['File'] = $meta['file'];
			}
			if (isset($meta['line'])) {
				$metaMsg['Line'] = $meta['line'];
			}
			$msg = '[' . $_this->jsonEncode($metaMsg) . ',' . $_this->jsonEncode($message, $skipFinalObjectEncode).']';
		}
		$lines = explode("\n", chunk_split($msg, 5000, "\n"));
		foreach ($lines as $i => $line) {
			if (empty($line)) {
				continue;
			}
			$header = sprintf('X-Wf-1-%s-1-%s', $structureIndex, $_this->_messageIndex);
			if (count($lines) > 2) {
				// Message needs to be split into multiple parts
				$_this->_sendHeader($header,
									(($i == 0) ? strlen($msg) : '')
									. '|' . $line . '|'
									. (($i < count($lines) - 2)? '\\' : '')
				);
			} else {
				$_this->_sendHeader($header, strlen($line) . '|' . $line . '|');
			}
			$_this->_messageIndex++;
			if ($_this->_messageIndex > 99999) {
				trigger_error(__('Maximum number (99,999) of messages reached!', true), E_USER_WARNING);
			}
		}
		$_this->_sendHeader('X-Wf-1-Index', $_this->_messageIndex - 1);
		return true;
	}
/**
 * undocumented function
 *
 * @param mixed $object Object or array to json encode
 * @param boolean $doIt
 * @access public
 * @return string
 **/
	function jsonEncode($object, $doIt = false) {
		if (function_exists('json_encode')) {
			return json_encode($object);
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
 * @return string UserAgent string of active client connection
 **/
	function getUserAgent() {
		return env('HTTP_USER_AGENT');
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
?>
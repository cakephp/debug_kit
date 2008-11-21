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
/**
 * get Instance of the singleton
 *
 * @access public
 * @return void
 */
	function &getInstance() {
		static $instance = array();
		if (!isset($instance[0]) || !$instance[0]) {
			$instance[0] =& new FireCake();
		}
		return $instance[0];
	}

/**
 * setOptions
 *
 * @param array $options Array of options to set.
 * @access public
 * @return void
 */
	function setOptions($options = array()) {
		if (empty($this->options)) {
			$this->options = array_merge($this->_defaultOptions, $options);
		} else {
			$this->options = array_merge($this->options, $options);
		}
	}
/**
 * fb - Send messages with FireCake to FirePHP
 *
 * @return void
 **/
	function fb() {
		
	}
}
?>
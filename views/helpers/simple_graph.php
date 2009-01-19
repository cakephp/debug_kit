<?php
/* SVN FILE: $Id$ */
/**
 * Simple Graph Helper
 *
 * Allows creation and display of extremely simple graphing elements
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
 * @package       debug_kit
 * @subpackage    debug_kit.views.helpers
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Helper', 'Html');

class SimpleGraphHelper extends AppHelper {
/**
 * Helpers
 * 
 * @var array
 */
	var $helpers = array('Html');
/**
 * Default settings to be applied to each Simple Graph
 * 
 * Allowed options:
 *   max => int
 *   width => int
 *   valueType => string (value, percentage)
 *   style => array
 * 
 * @var array
 */
	var $__defaultSettings = array(
		'max' => 100,
		'width' => 150,
		'valueType' => 'value',
	);
/**
 * 
 * @param $value Value to be graphed
 * @param $options Graph options
 * @return string Html graph
 */
	function bar($value, $options = array()) {
		$settings = array_merge($this->__defaultSettings, $options);
		extract($settings);
		
		if ($valueType == 'percentage') {
			$value = $value / $width;
		} else {
			$value = $value / $max * $width;
		}
		$value = round($value);
		
		return $this->Html->div(
			'debug-kit-graph-bar',
			$this->Html->div(
				'debug-kit-graph-bar-value',
				null,
				array('style' => "width: {$value}px")),
			array('style' => "width: {$width}px;"),
			false);
	}
}
?>
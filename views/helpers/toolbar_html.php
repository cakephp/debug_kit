<?php
/* SVN FILE: $Id$ */
/**
 * Short description for toolbar_html.php
 *
 * Long description for toolbar_html.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       debug_kit
 * @subpackage    debug_kit.views.helpers
 * @since         v 1.0
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('helper', 'DebugKit.Toolbar');
class ToolbarHtmlHelper extends ToolbarHelper {
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Javascript');
/**
 * Recursively goes through an array and makes neat HTML out of it.
 *
 * @param mixed $values Array to make pretty.
 * @param int $openDepth Depth to add open class
 * @param int $currentDepth current depth.
 * @return string
 **/
	function makeNeatArray($values, $openDepth = 0, $currentDepth = 0) {
		$className ="neat-array depth-$currentDepth";
		if ($openDepth > $currentDepth) {
			$className .= ' expanded';
		}
		$nextDepth = $currentDepth + 1;
		$out = "<ul class=\"$className\">";
		if (!is_array($values)) {
			if (is_bool($values)) {
				$values = array($values);
			}
			if (is_null($values)) {
				$values = array(null);
			}
		}
		foreach ($values as $key => $value) {
			$out .= '<li><strong>' . $key . '</strong>';
			if ($value === null) {
				$value = '(null)';
			}
			if ($value === false) {
				$value = '(false)';
			}
			if ($value === true) {
				$value = '(true)';
			}
			if (empty($value) && $value != 0) {
				$value = '(empty)';
			}
			if (is_array($value) && !empty($value)) {
				$out .= $this->makeNeatArray($value, $openDepth, $nextDepth);
			} else {
				$out .= $value;
			}
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}
/**
 * send method
 *
 * @return void
 * @access protected
 */
	function _send() {
		$view =& ClassRegistry::getObject('view');
		$head = $this->Html->css('/debug_kit/css/debug_toolbar');
		if (isset($view->viewVars['debugToolbarJavascript'])) {
			foreach ($view->viewVars['debugToolbarJavascript'] as $script) {
				if ($script) {
					$head .= $this->Javascript->link($script);
				}
			}
		}
		if (preg_match('#</head>#', $view->output)) {
			$view->output = preg_replace('#</head>#', $head . "\n</head>", $view->output, 1);
		}
		$toolbar = $view->element('debug_toolbar', array('plugin' => 'debug_kit'), true);
		if (preg_match('#</body>\s*</html>#', $view->output)) {
			$view->output = preg_replace('#</body>\s*</html>#', $toolbar . "\n</body>\n</html>", $view->output, 1);
		}
		Configure::write('debug', 0);
	}
}
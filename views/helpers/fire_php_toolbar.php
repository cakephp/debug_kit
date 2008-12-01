<?php
/* SVN FILE: $Id$ */
/**
 * FirePHP Toolbar Helper
 *
 * Injects the toolbar elements into non-HTML layouts via FireCake.
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
App::import('helper', 'DebugKit.Toolbar');
App::import('Vendor', 'DebugKit.FireCake');

class FirePhpToolbarHelper extends ToolbarHelper {
/**
 * send method
 *
 * @return void
 * @access protected
 */
	function _send() {
		$view =& ClassRegistry::getObject('view');
		foreach($view->viewVars['debugToolbarPanels'] as $panel => $data) {
			$table = $data['content'];
			$label = $data['elementName'];
			FireCake::info($table, $label);
		}
		//$view->element('debug_toolbar', array('plugin' => 'debug_kit', 'disableTimer' => true));
		Configure::write('debug', 1);
	}
/**
 * makeNeatArray.
 *
 * wraps FireCake::dump() allowing panel elements to continue functioning
 *
 * @param string $values 
 * @return void
 */	
	function makeNeatArray($values) {
		FireCake::info($values);
	}
}
?>
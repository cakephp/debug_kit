<?php
/* SVN FILE: $Id$ */
/**
 * Toolbar history state view.
 *
 * 
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
 * @package         cake.debug_kit
 * @subpackage      cake.debug_kit.views
 * @version         
 * @modifiedby      
 * @lastmodified    
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$panels = array();
foreach ($toolbarState as $panelName => $panel) {
	$panels[$panelName] = $this->element($panel['elementName'], array(
		'content' => $panel['content'], 
		'plugin' => $panel['plugin']
	));
}
echo $javascript->object($panels);
?>
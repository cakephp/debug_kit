/* SVN FILE: $Id: cake.generic.css 7337 2008-07-13 23:28:45Z mark_story $ */
/**
 * Debug Toolbar Javascript.  Requires jQuery
 *
 * Custom Debug View class, helps with development.
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
 * @copyright		Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link			http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.cake.libs.
 * @since			CakePHP v 1.2.0.4487
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

$(document).ready(function(){
	DebugKit.NeatArray();
});

var DebugKit = {};
/**
 * Create all behaviors for neat array elements
 *
 */
DebugKit.NeatArray = function() {
	$('.neat-array').find('li:has(ul)').toggle(
			function() {
				$(this).toggleClass('expanded').find('ul:first').show();
			},
			function() {
				$(this).toggleClass('expanded').find('ul:first').hide();
			}
		).addClass('expandable').find('ul').hide();
}
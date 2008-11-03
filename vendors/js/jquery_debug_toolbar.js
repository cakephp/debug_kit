/* SVN FILE: jQueryId: cake.generic.css 7337 2008-07-13 23:28:45Z mark_story jQuery */
/**
 * Debug Toolbar Javascript.  jQuery 1.2.x compatible
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
 * @version			jQueryRevisionjQuery
 * @modifiedby		jQueryLastChangedByjQuery
 * @lastmodified	jQueryDatejQuery
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
jQuery.noConflict();

jQuery(document).ready(function(){
	DebugKit.Toolbar();
	DebugKit.NeatArray();
});

var DebugKit = {};
/**
 * Create all behaviors for neat array elements
 *
 */
DebugKit.NeatArray = function() {
	jQuery('.neat-array').find('li:has(ul)').toggle(
		function() {
			jQuery(this).toggleClass('expanded').removeClass('collapsed').find('ul:first').show();
		},
		function() {
			jQuery(this).toggleClass('expanded').addClass('collapsed').find('ul:first').hide();
		}
	).addClass('expandable').addClass('collapsed').find('ul').hide();
}
/**
 * Add behavior for toolbar buttons
 *
 */
DebugKit.Toolbar = function() {
	var tabCollection = jQuery('#debug-kit-toolbar li > div');
	 
	jQuery('#debug-kit-toolbar .panel-tab a').click(
		function(e){
			e.preventDefault();
			var targetPanel = jQuery(this.hash + '-tab');
			if (targetPanel.hasClass('active')) {
				tabCollection.hide().removeClass('active');
			} else {
				tabCollection
					.hide().removeClass('active')
					.filter(this.hash + '-tab').show().addClass('active');
			}
			jQuery('#debug-kit-toolbar .panel-tab a').removeClass('active');
			jQuery(this).addClass('active');
		}
	);
	
	//enable hiding of toolbar.
	var panelButtons = jQuery('#debug-kit-toolbar .panel-tab:not(.panel-tab.icon)');
	jQuery('#debug-kit-toolbar #hide-toolbar').toggle(
		function() {
			panelButtons.hide();
		},
		function() {
			panelButtons.show();
		}
	);
}
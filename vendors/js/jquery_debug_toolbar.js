/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Javascript.  jQuery 1.2.x compatible.
 *
 * Long description here.
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
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.cake.libs.
 * @since         CakePHP v 1.2.0.4487
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

(function($) {
$(document).ready(function(){
	DebugKit.Toolbar();
	DebugKit.NeatArray();
  DebugKit.History();
});

var DebugKit = {};
/**
 * Create all behaviors for neat array elements
 *
 */
DebugKit.NeatArray = function() {
	$('.neat-array').find('li:has(ul)').toggle(
		function() {
			$(this).toggleClass('expanded').removeClass('collapsed').find('ul:first').show();
		},
		function() {
			$(this).toggleClass('expanded').addClass('collapsed').find('ul:first').hide();
		}
	).addClass('expandable').addClass('collapsed').find('ul').hide();
}
/**
 * Add behavior for toolbar buttons
 *
 */
DebugKit.Toolbar = function() {
	var tabCollection = $('#debug-kit-toolbar li > div');
	 
	$('#debug-kit-toolbar .panel-tab > a').click(
		function(e){
			e.preventDefault();
			var targetPanel = $(this.hash + '-tab');
			if (targetPanel.hasClass('active')) {
				tabCollection.hide().removeClass('active');
			} else {
				tabCollection
					.hide().removeClass('active')
					.filter(this.hash + '-tab').show().addClass('active');
			}
			$('#debug-kit-toolbar .panel-tab > a').removeClass('active');
			$(this).addClass('active');
		}
	);
	
	//enable hiding of toolbar.
	var panelButtons = $('#debug-kit-toolbar .panel-tab:not(.panel-tab.icon)');
	$('#debug-kit-toolbar #hide-toolbar').toggle(
		function() {
			panelButtons.hide();
		},
		function() {
			panelButtons.show();
		}
	);
}
/**
 * Handle hiding/displaying panels from previous requests
 *
 */
DebugKit.History = function() {
  $('.history-link').click(function() {
    var id = $(this).attr('href').replace('#', '');
    
    $('.history-link').removeClass('active');
    $(this).addClass('active');
    
    $('.panel-content-data').hide();
    $('.panel-content' + id).show();
    
    return false;
  });
}
})(jQuery);
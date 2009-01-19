/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Javascript.  Mootools 1.2 compatible.
 *
 * Requires Class, Event, Element, and Selectors 
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

window.addEvent('domready', function() {
	new DebugKit();
});
var DebugKit = new Class({
	initialize : function() {
		this.neatArray();
		this.toolbar();
	},
/**
 * Create all behaviors for neat array elements
 */
	neatArray : function() {
		$$('#debug-kit-toolbar .neat-array li').each(function(listItem) {
			var subUl = listItem.getElement('ul');
			if (subUl) {
				listItem.addClass('expandable').addClass('collapsed');
				subUl.setStyle('display', 'none').set('state', 'closed');
				listItem.addEvent('click', function(event) {
					event.stop();
					this.toggleClass('expanded').toggleClass('collapsed');
					if (subUl.get('state') == 'closed') {
						subUl.setStyle('display', 'block').set('state', 'open');
					} else {
						subUl.setStyle('display', 'none').set('state', 'closed');
					}
				}) 
			}
		});
	},

	/**
	 * Add behavior for toolbar buttons
	 */
	toolbar : function() {
		var tabCollection = $$('#debug-kit-toolbar li > div');

		$$('#debug-kit-toolbar .panel-tab > a').addEvent('click', function(event) {
			event.stop();
			var buttonId = this.hash.substring(1, this.hash.length) + '-tab';
			var targetPanel = $(buttonId);
			if (!targetPanel) return;
			$$('#debug-kit-toolbar .panel-tab > a').removeClass('active');
			if (targetPanel.hasClass('active')) {
				tabCollection.removeClass('active').setStyle('display', 'none');
			} else {
				tabCollection.setStyle('display', 'none').removeClass('active');
				targetPanel.addClass('active').setStyle('display', 'block');
				this.addClass('active');
			}
		});

		//enable hiding of toolbar.
		var panelButtons = $$('#debug-kit-toolbar .panel-tab:not(.panel-tab.icon)');
		var toolbarHide = $('hide-toolbar').set('state', 'open');
		toolbarHide.addEvent('click', function(event) {
			event.stop();
			var state = this.get('state');
			if (state == 'open') {
				panelButtons.setStyle('display', 'none');
				this.set('state', 'closed')
			} else {
				panelButtons.setStyle('display');
				this.set('state', 'open');
			}
		});
	}
});
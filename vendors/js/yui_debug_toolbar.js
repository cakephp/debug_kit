/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Javascript.  YUI 2.6 compatible.
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

YAHOO.namespace('CakePHP.DebugKit');

YAHOO.CakePHP.DebugKit = function() {

	var Event    = YAHOO.util.Event;
	var Dom      = YAHOO.util.Dom;
	var Selector = YAHOO.util.Selector;
	
	
	var toggle = function(el) {
 		Dom.setStyle(el, 'display', ((Dom.getStyle(el, 'display') == 'none') ? '' : 'none'));
	};

	var toggleClass = function(element, className) {
		(Dom.hasClass(element, className)) ? Dom.removeClass(element, className) : Dom.addClass(element, className);
	};
	
	
	
	var toolbar = function() {
		var tabCollection = Selector.query('#debug-kit-toolbar li > div');
		
		Dom.batch(Selector.query('#debug-kit-toolbar .panel-tab > a'), function(el) {
			Event.on(el, 'click', function(ev) {
				Event.preventDefault(ev);
				targetPanel =Dom.get(el.hash.replace(/#/, '') + '-tab');

				if (Dom.hasClass(targetPanel, 'active')) {
					Dom.batch(tabCollection, function(ele) {
						toggle(ele);
						Dom.removeClass(ele, 'active');
						Dom.setStyle(ele, 'display', '');
					});
				} else {
					Dom.batch(tabCollection, function(ele) {
						toggle(ele);
						Dom.removeClass(ele, 'active');
						
						if (targetPanel && targetPanel.id == ele.id) {
							Dom.setStyle(ele, 'display', 'block');
							Dom.addClass(ele, 'active');
						}
					});
				}
				
				Dom.removeClass(Selector.query('#debug-kit-toolbar .panel-tab > a'), 'active');
				Dom.addClass(el, 'active');
			});
		});
		
	};
	

	
	var neatArray = function() {
		nodes   = Selector.query('#debug-kit-toolbar .panel-content > ul.neat-array li > ul');
		
		if (nodes.length > 0) {
			Dom.batch(nodes, function(el) {
				
				var parent = el.parentNode;
				
				Dom.addClass(parent, 'collapsed');
				Dom.addClass(parent, 'expandable');
				toggle(nodes);
				
				Event.on(parent, 'click', function(ev) {
					sub = Selector.query('ul', parent);
					toggleClass(parent, 'expanded');
					toggleClass(parent, 'collapsed');
					toggle(sub);
				});
			});
		}	
	};
	
	var panelButtons = function() {
		Event.on('hide-toolbar', 'click', function(ev) {
			Event.preventDefault(ev);

			Dom.getElementsByClassName('panel-tab', 'li', 'debug-kit-toolbar', function (el) {
				if (!Dom.hasClass(el, 'icon')) {
					toggle(el);
				}
			});			
		});
	};
		
	return {
		initialize: function() {
			neatArray();
			toolbar();
			panelButtons();
		}
	};
}(); // Execute annonymous closure & return results

YAHOO.util.Event.onDOMReady(YAHOO.CakePHP.DebugKit.initialize);
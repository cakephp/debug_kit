/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Javascript.
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

var DebugKit = function(id) {
	var undefined,
		elements = {},
		panels = {},
		hidden = false;

	this.initialize = function(id) {
		elements.toolbar = document.getElementById(id || 'debug-kit-toolbar');

		if (elements.toolbar === undefined) {
			throw new Exception('Toolbar not found, make sure you loaded it.');
		}

		for (var i in elements.toolbar.childNodes) {
			var element = elements.toolbar.childNodes[i];
			if (element.nodeName && element.id === 'panel-tabs') {
				elements.panel = element;
				break;
			}
		}

		for (var i in elements.panel.childNodes) {
			var element = elements.panel.childNodes[i];
			if (element.className && element.className.match(/panel-tab/)) {
				this.addPanel(element);
			}
		}

		var lists = document.getElementsByTagName('ul'), index = 0;
		while (lists[index] !== undefined) {
			var element = lists[index];
			if (element.className && element.className.match(/neat-array/)) {
				neatArray(element);
			}
			++index;
		}
	}
/**
 * Add a panel to the toolbar
 */
	this.addPanel = function(tab, callback) {
		if (!tab.nodeName || tab.nodeName.toUpperCase() !== 'LI') {
			throw new Exception('Toolbar not found, make sure you loaded it.');
		}
		var panel = {
			id : false,
			element : tab,
			callback : callback,
			button : undefined,
			content : undefined,
			active : false
		};
		for (var i in tab.childNodes) {
			var element = tab.childNodes[i],
				tag = element.nodeName? element.nodeName.toUpperCase(): false;
			if (tag === 'A') {
				panel.id = element.hash.replace(/^#/, '');
				panel.button = element;
			} else if (tag === 'DIV') {
				panel.content = element;
			}
		}
		if (!panel.id) {
			throw new Exception('invalid element');
		}

		if (panel.button.id && panel.button.id === 'hide-toolbar') {
			panel.callback = this.toggleToolbar;
		}

		if (panel.callback !== undefined) {
			panel.button.onclick = function() { return panel.callback(); };
		} else {
			panel.button.onclick = function() { return window.DebugKit.togglePanel(panel.id); };
		}

		panels[panel.id] = panel;
		return panel.id;
	};
/**
 * Hide/show the toolbar (minimize cake)
 */	
	this.toggleToolbar = function() {
		for (var i in panels) {
			var panel = panels[i],
				display = hidden? 'block': 'none';
			if (panel.content !== undefined) {
				panel.element.style.display = display;
			}
		}
		hidden = !hidden;
		return true;
	};
/**
 * Toggle a panel
 */
	this.togglePanel = function(id) {
		if (panels[id] && panels[id].active) {
			this.deactivatePanel(true);
		} else {
			this.deactivatePanel(true);
			this.activatePanel(id);
		}
	}
/**
 * Make a panel active.
 */
	this.activatePanel = function(id, unique) {
		if (panels[id] !== undefined && !panels[id].active) {
			var panel = panels[id];
			this.deactivatePanel(true);
			if (panel.content !== undefined) {
				panel.content.style.display = 'block';
			}
			panel.button.className = panel.button.className.replace(/^(.*)$/, '$1 active');
			panel.active = true;
			return true;
		}
		return false;
	};
/**
 * Deactivate a panel.  use true to hide all panels.
 */
	this.deactivatePanel = function(id) {
		if (id === true) {
			for (var i in panels) {
				this.deactivatePanel(i);
			}
			return true;
		}
		if (panels[id] !== undefined && panels[id].active) {
			var panel = panels[id];
			if (panel.content !== undefined) {
				panel.content.style.display = 'none';
			}
			panel.button.className = panel.button.className.replace(/ ?(active) ?/, '');
			panel.active = false;
			return true;
		}
		return false;
	};
/**
 * Add neat array functionality.
 */
	function neatArray(list) {
		if (!list.className.match(/depth-0/)) {
			var item = list.parentNode;
			list.style.display = 'none';
			item.className = (item.className || '').replace(/^(.*)$/, '$1 expandable collapsed');
			item.onclick = function(event) {
				//var element = (event === undefined)? this: event.target;
				var element = this,
					event = event || window.event,
					act = Boolean(item === element),
					hide = Boolean(list.style.display === 'block');
				if (act && hide) {
					list.style.display = 'none';
					item.className = item.className.replace(/expanded|$/, 'collapsed');
				} else if (act) {
					list.style.display = 'block';
					item.className = item.className.replace('collapsed', 'expanded');
				}
				
				if (event.cancelBubble !== undefined) {
					event.cancelBubble = true;
				}
				return false;
			}
		}
	}

	this.initialize(id);
}

DebugKit.install = function() {
	var initializer = window.onload || function() {};
	window.onload = function() {
		initializer();
		// makes DebugKit a singletone instance
		window.DebugKit = new DebugKit();
	}
}

DebugKit.install();
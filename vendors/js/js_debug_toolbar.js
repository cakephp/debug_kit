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
		toolbarHidden = false,
		Cookie = new DebugKit.Util.Cookie(),
		Util = DebugKit.Util;

	this.initialize = function(id) {
		elements.toolbar = document.getElementById(id || 'debug-kit-toolbar');

		if (elements.toolbar === undefined) {
			throw('Toolbar not found, make sure you loaded it.');
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
		
		this.deactivatePanel(true);
		if (Cookie.read('toolbarDisplay') == 'none') {
			toolbarHidden = true;
			this.toggleToolbar()
		}
	}
/**
 * Add a panel to the toolbar
 */
	this.addPanel = function(tab, callback) {
		if (!tab.nodeName || tab.nodeName.toUpperCase() !== 'LI') {
			throw('Toolbar not found, make sure you loaded it.');
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
				tag = element.nodeName ? element.nodeName.toUpperCase() : false;
			if (tag === 'A') {
				panel.id = element.hash.replace(/^#/, '');
				panel.button = element;
			} else if (tag === 'DIV') {
				panel.content = element;
			}
		}
		if (!panel.id) {
			throw('invalid element');
		}

		if (panel.button.id && panel.button.id === 'hide-toolbar') {
			panel.callback = this.toggleToolbar;
		}

		var callbackName = "activate" + panel.id.replace('-tab', '');
		if (this[callbackName] !== undefined) {
			this[callbackName](panel);
		}

		if (panel.callback !== undefined) {
			Util.addEvent(panel.button, 'click', function(event) {
				event || window.event;
				event.preventDefault();
				return panel.callback();
			});
		} else {
			Util.addEvent(panel.button, 'click', function(event) {
				event || window.event;
				event.preventDefault();
				return window.DebugKit.togglePanel(panel.id);
			})
		}
		panels[panel.id] = panel;
		return panel.id;
	};
/**
 * Hide/show the toolbar (minimize cake)
 */	
	this.toggleToolbar = function() {
		var display = toolbarHidden ? 'block' : 'none';
		for (var i in panels) {
			var panel = panels[i];
			if (panel.content != undefined) {
				panel.element.style.display = display;
				Cookie.write('toolbarDisplay', display);
			}
		}
		toolbarHidden = !toolbarHidden;
		return false;
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
		if (panels[id] !== undefined) {
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
 * Activate history panel.
 * adds events to all the button
 */	
	this.activatehistory = function(panel) {
		var anchors = panel.element.getElementsByTagName('A'),
			historyLinks = [];
			
		for (var i in anchors) {
			var button = anchors[i];
			if (button.className && button.className.match(/history-link/)) {
				historyLinks.push(button);
			}
		}
		for (var i in historyLinks) {
			var button = historyLinks[i];

			Util.addEvent(button, 'click', function (event) {
				event.preventDefault();
				var id = this.hash.replace(/^#/, '');
				for (var i in historyLinks) {
					historyLinks[i].className = historyLinks[i].className.replace(/ ?(active) ?/, '');
				}
				this.className = this.className.replace(/^(.*)$/, '$1 active');
				
				//hide all panel-content-data
				for (var i in panels) {
					if (!panels[i].content) {
						continue;
					}
					var curPanel = panels[i].content;
					var panelDivs = curPanel.getElementsByTagName('DIV');
					for (var j in panelDivs) {
						var panelData = panelDivs[j];
						if (panelData.className && panelData.className.match(/panel-content-data/)) {
							panelData.style.display = 'none';
						}
						var regex = new RegExp('panel-content' + id);
						if (panelData.className && panelData.className.match(regex)) {
							panelData.style.display = 'block';
							var panelWrapper = panelData.parentNode;
							if (!panelData.parentNode.className.match(/panel-history-active/)) {
								var newClass = panelWrapper.className.replace(/^(.*)$/, '$1 panel-history-active');
								panelWrapper.className = newClass;
							}
						}
					}
					if (id == 0) {
						console.log(panels[i].content.className);
						var newClass = panels[i].content.className.replace(/ ?(panel-history-active) ?/, '');
						console.log(newClass);
						panels[i].content.className = newClass;
					}
				}
			});
		}
	};
/**
 * Add neat array functionality.
 */
	var neatArray = function(list) {
		if (!list.className.match(/depth-0/)) {
			var item = list.parentNode;
			list.style.display = 'none';
			item.className = (item.className || '').replace(/^(.*)$/, '$1 expandable collapsed');
			Util.addEvent(item, 'click', function(event) {
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
			});
		}
	}
	this.initialize(id);
}

/** 
 * Utility functions for debugKit Js
 */
DebugKit.Util = {};

DebugKit.Util.Cookie = function() {
	var cookieLife = 60;
/**
 * Write to cookie
 * @param [string] name Name of cookie to write.
 * @param [mixed] value Value to write to cookie.
 */
	this.write = function (name, value) {
		var date = new Date();
		date.setTime(date.getTime() + (cookieLife * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
		document.cookie = name + "=" + value + expires + "; path=/";
		return true;
	}
/**
 * Read from the cookie
 * @param [string] name Name of cookie to read.
 */	
	this.read = function (name) {
		name = name + '=';
		var cookieJar = document.cookie.split(';');
		for (var i = 0; i < cookieJar.length; i++) {
			var chips = cookieJar[i];
			//trim leading spaces
			while (chips.charAt(0) == ' ') {
				chips = chips.substring(1, chips.length);
			}
			if (chips.indexOf(name) == 0) {
				return chips.substring(name.length, chips.length)
			}
		}
		return false;
	}
/**
 * Delete a cookie by name.
 */
	this.del = function (name) {
		var date = new Date();
		date.setFullYear(2000,0,1);
		var expires = " ; expires=" + date.toGMTString();
		document.cookie = name + "=" + expires + "; path=/";
	}
}

/**
 * Cross browser domready handler.
 *
 */
DebugKit.Util.domready = function(callback) {
	if (document.addEventListener) {
		return document.addEventListener("DOMContentLoaded", callback, false);
	}

	if (document.all && !window.opera){ 
		//Define a "blank" external JavaScript tag
		document.write('<script type="text/javascript" id="domreadywatcher" defer="defer" src="javascript:void(0)"><\/script>');
		var contentloadtag = document.getElementById("domreadywatcher")
		contentloadtag.onreadystatechange = function(){
			if (this.readyState == "complete") {
				callback();
			}
		}
		return;
	}

	if (/Webkit/i.test(navigator.userAgent)){
		var _timer = setInterval(function(){
		if (/loaded|complete/.test(document.readyState)) {
			clearInterval(_timer)
			callback();
		}}, 10);
	}
}
/**
 * Cross browser event registration.
 */
DebugKit.Util.addEvent = function(element, type, handler, capture) {
	capture = (capture == undefined) ? false : capture;
	if (element.addEventListener) {
		return element.addEventListener(type, handler, capture);
	}
	if (element.attachEvent) {
		type = 'on' + type;
		return element.attachEvent(type, handler);
	}
	return obj['on' + type] = handler;
}


DebugKit.Util.domready(function() {
	window.DebugKit = new DebugKit();
});
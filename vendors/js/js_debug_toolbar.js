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

var DebugKit = function (id) {
	var elements = {},
		panels = {},
		toolbarHidden = false,
		Cookie = new DebugKit.Util.Cookie(),
		Util = DebugKit.Util,
		Request = DebugKit.prototype.Request,
		Element = DebugKit.Util.Element;

	this.initialize = function (id) {
		var i, element, lists, index;
		elements.toolbar = document.getElementById(id || 'debug-kit-toolbar');

		if (elements.toolbar === undefined) {
			throw('Toolbar not found, make sure you loaded it.');
		}
		

		for (i in elements.toolbar.childNodes) {
			element = elements.toolbar.childNodes[i];
			if (element.nodeName && element.id === 'panel-tabs') {
				elements.panel = element;
				break;
			}
		}

		for (i in elements.panel.childNodes) {
			element = elements.panel.childNodes[i];
			if (Element.hasClass(element, 'panel-tab')) {
				this.addPanel(element);
			}
		}

		lists = document.getElementsByTagName('ul');
		this.makeNeatArray(lists);

		this.deactivatePanel(true);
		var toolbarState = Cookie.read('toolbarDisplay');
		if (toolbarState != 'block') {
			toolbarHidden = false;
			this.toggleToolbar();
		}
		return this;
	};
/**
 * Add a panel to the toolbar
 */
	this.addPanel = function (tab, callback) {
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
				event = event || window.event;
				event.preventDefault();
				return panel.callback();
			});
		} else {
			Util.addEvent(panel.button, 'click', function(event) {
				event = event || window.event;
				event.preventDefault();
				return DebugKit.togglePanel(panel.id);
			});
		}
		panels[panel.id] = panel;
		return panel.id;
	};
/**
 * Hide/show the toolbar (minimize cake)
 */	
	this.toggleToolbar = function () {
		var display = toolbarHidden ? 'block' : 'none';
		for (var i in panels) {
			var panel = panels[i];
			if (panel.content !== undefined) {
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
	this.togglePanel = function (id) {
		if (panels[id] && panels[id].active) {
			this.deactivatePanel(true);
		} else {
			this.deactivatePanel(true);
			this.activatePanel(id);
		}
	};
/**
 * Make a panel active.
 */
	this.activatePanel = function (id, unique) {
		if (panels[id] !== undefined && !panels[id].active) {
			var panel = panels[id];
			if (panel.content !== undefined) {
				Element.show(panel.content);
			}
			Element.addClass(panel.button, 'active');
			panel.active = true;
			return true;
		}
		return false;
	};
/**
 * Deactivate a panel.  use true to hide all panels.
 */
	this.deactivatePanel = function (id) {
		if (id === true) {
			for (var i in panels) {
				this.deactivatePanel(i);
			}
			return true;
		}
		if (panels[id] !== undefined) {
			var panel = panels[id];
			if (panel.content !== undefined) {
				Element.hide(panel.content);
			}
			Element.removeClass(panel.button, 'active');
			panel.active = false;
			return true;
		}
		return false;
	};
/**
 * Activate history panel.
 * adds events to all the button
 */	
	this.activatehistory = function (panel) {
		var anchors = panel.element.getElementsByTagName('A'),
			historyLinks = [], 
			i = 0, j =0, 
			button, self = this;
			
		for (i in anchors) {
			button = anchors[i];
			if (Element.hasClass(button, 'history-link')) {
				historyLinks.push(button);
			}
		}

		/**
		 * Private methods to handle JSON response and insertion of 
		 * new content.
		 */
		var switchHistory = function (response) {
			try {
				var responseJson = eval( '(' + response.response.text + ')');
			} catch (e) {
				alert('Could not convert JSON response');
				return false;
			}

			for (var i in historyLinks) {
				Element.removeClass(historyLinks[i], 'loading');
			}

			for (var id in panels) {
				var panel = panels[id];
				if (panel.content === undefined || responseJson[id] === undefined) {
					continue;
				}
				var panelDivs = panel.content.childNodes;
				for (var i in panelDivs) {

					//toggle history element, hide current request one.
					var panelContent = panelDivs[i],
						tag = panelContent.nodeName ? panelContent.nodeName.toUpperCase() : false;
					if (tag === 'DIV' && Element.hasClass(panelContent, 'panel-content-history')) {
						var panelId = panelContent.id.replace('-history', '');
						if (responseJson[panelId]) {
							panelContent.innerHTML = responseJson[panelId];
							var lists = panelContent.getElementsByTagName('UL');
							self.makeNeatArray(lists);
						}
						Element.show(panelContent);
					} else if (tag === 'DIV') {
						Element.hide(panelContent);
					}
				}
			}
		};
		
		/**
		 * Private method to handle restoration to current request.
		 */
		var restoreCurrentState = function () {
			var id, i, panelContent, tag;

			for (id in panels) {
				panel = panels[id];
				if (panel.content === undefined) {
					continue;
				}
				var panelDivs = panel.content.childNodes;
				for (i in panelDivs) {
					panelContent = panelDivs[i];
					tag = panelContent.nodeName ? panelContent.nodeName.toUpperCase() : false;
					if (tag === 'DIV' && Element.hasClass(panelContent, 'panel-content-history')) {
						Element.hide(panelContent);
					} else if (tag === 'DIV') {
						Element.show(panelContent);
					}
				}
			}
		};

		var handleHistoryLink = function (event) {
			event.preventDefault();

			for (i in historyLinks) {
				Element.removeClass(historyLinks[i], 'active');
			}
			Element.addClass(this, 'active loading');
			
			if (this.id === 'history-restore-current') {
				restoreCurrentState();
				return false;
			}

			var remote = new Request({
				onComplete : switchHistory,
				onFail : function () {
					alert('History retrieval failed');
				}
			});
			remote.send(this.href);
		};

		for (i in historyLinks) {
			button = historyLinks[i];
			Util.addEvent(button, 'click', handleHistoryLink);
		}
	};

	this.makeNeatArray = function (lists) {
		i = 0;
		while (lists[i] !== undefined) {
			var element = lists[i];
			if (Element.hasClass(element, 'neat-array')) {
				neatArray(element);
			}
			++i;
		}
	};
/**
 * Add neat array functionality.
 */
	var neatArray = function (list) {
		if (!list.className.match(/depth-0/)) {
			var item = list.parentNode;
			Element.hide(list);
			Element.addClass(item, 'expandable collapsed');
			Util.addEvent(item, 'click', function (event) {
				var element = this,
					event = event || window.event,
					act = Boolean(item === element),
					hide = Boolean(list.style.display === 'block');
				if (act && hide) {
					Element.hide(list);
					item.className = item.className.replace(/expanded|$/, 'collapsed');
				} else if (act) {
					Element.show(list);
					item.className = item.className.replace('collapsed', 'expanded');
				}
				
				if (event.cancelBubble !== undefined) {
					event.cancelBubble = true;
				}
				return false;
			});
		}
	};

	this.initialize(id);
};

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
	};
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
				return chips.substring(name.length, chips.length);
			}
		}
		return false;
	};
/**
 * Delete a cookie by name.
 */
	this.del = function (name) {
		var date = new Date();
		date.setFullYear(2000,0,1);
		var expires = " ; expires=" + date.toGMTString();
		document.cookie = name + "=" + expires + "; path=/";
	};
};

/**
 * Cross browser domready handler.
 */
DebugKit.Util.domready = function(callback) {
	if (document.addEventListener) {
		return document.addEventListener("DOMContentLoaded", callback, false);
	}

	if (document.all && !window.opera) { 
		//Define a "blank" external JavaScript tag
		document.write('<script type="text/javascript" id="domreadywatcher" defer="defer" src="javascript:void(0)"><\/script>');
		var contentloadtag = document.getElementById("domreadywatcher");
		contentloadtag.onreadystatechange = function (){
			if (this.readyState == "complete") {
				callback();
			}
		};
		return;
	}

	if (/Webkit/i.test(navigator.userAgent)){
		var _timer = setInterval(function (){
			if (/loaded|complete/.test(document.readyState)) {
				clearInterval(_timer);
				callback();
			}
		}, 10);
	}
};

/**
 * Cross browser event registration.
 */
DebugKit.Util.addEvent = function(element, type, handler, capture) {
	capture = (capture === undefined) ? false : capture;
	if (element.addEventListener) {
		element.addEventListener(type, handler, capture);
	} else if (element.attachEvent) {
		type = 'on' + type;
		element.attachEvent(type, handler);
	} else {
		type = 'on' + type;
		element[type] = handler;
	}
};

/**
 * Simple Element manipulation shortcuts.
 */
DebugKit.Util.Element = {
	hasClass : function (element, className) {
		if (!element.className) {
			return false;
		}
		return element.className.match(new RegExp(className));
	},
	addClass : function (element, className) {
		if (!element.className) {
			element.className = '';
		}
		element.className = element.className.replace(/^(.*)$/, '$1 ' + className);
	},
	removeClass : function (element, className) {
		if (!element.className) {
			return false;
		} 
		element.className = element.className.replace(new RegExp(' ?(' + className +') ?'), '');
	},
	show : function (element) {
		element.style.display = 'block';
	},
	hide : function (element) {
		element.style.display = 'none';
	}
};
/**
 * Object merge takes any number of arguments and glues them together
 * @param [Object] one first object
 * @return object 
 */
DebugKit.prototype.merge = function() {
	var out = {};
	for (var i = 0; i < arguments.length; i++) {
		var current = arguments[i];
		for (prop in current) {
			if (current[prop] !== undefined){
				out[prop] = current[prop];
			}
		}
	}
	return out;
};

/**
 * Simple wrapper for XmlHttpRequest objects.
 */
DebugKit.prototype.Request = function (options) {
	var _defaults = {
		onComplete : function (){},
		onRequest : function (){},
		onFail : function (){},
		method : 'GET',
		async : true,
		headers : {
			'X-Requested-With': 'XMLHttpRequest',
			'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
		}
	};

	var self = this;
	this.options = DebugKit.merge(_defaults, options);
	this.options.method = this.options.method.toUpperCase();

	var ajax = this.createObj();
	this.transport = ajax;

	//event assignment
	this.onComplete = this.options.onComplete;
	this.onRequest = this.options.onRequest;
	this.onFail = this.options.onFail;

	this.send = function (url, data) {
		if (this.options.method == 'GET' && data) {
			url = url + ( (url.charAt(url.length -1) == '?') ? '&' : '?') + data; //check for ? at the end of the string
			data = null;
		}
		//open connection
		this.transport.open(this.options.method, url, this.options.async);

		//set statechange and pass the active XHR object to it.  From here it handles all status changes.
		this.transport.onreadystatechange = function () {
			self.onReadyStateChange.apply(self, arguments);
		};
		for (var key in this.options.headers) {
			this.transport.setRequestHeader(key, this.options.headers[key]);
		}
		this.onRequest();
		this.transport.send(data);
	};
};

DebugKit.prototype.Request.prototype.onReadyStateChange = function (){
	if (this.transport.readyState !== 4) {
		return;
	}
	if (this.transport.status == 200 || this.transport.status > 300 && this.transport.status < 400 ) {
		this.response = { 
			xml: this.transport.responseXML,
			text: this.transport.responseText
		};
		
		if (typeof this.onComplete == 'function') {
			this.onComplete.apply(this, [this, this.response]);
		} else {
			return this.response;
		}
	} else if (this.transport.status > 400) {
		if (typeof this.onFail == 'function') {
			this.onFail.apply(this, []);
		} else {
			console.error('request failed');
		}
	}
};
/**
 * Creates cross-broswer XHR object used for requests
 */
DebugKit.prototype.Request.prototype.createObj = function(){
	var request = null;
	try {
		request = new XMLHttpRequest();
	} catch (MS) {
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (old_MS) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(failure) {
				request = null;
			}
		}
	}
	return request;
};


DebugKit.Util.domready(function() {
	window.DebugKit = new DebugKit();
});

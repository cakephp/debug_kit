/**
 * CakePHP DebugKit Prototype javascript adapter.
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

Event.observe(window, 'load', function() {
	DebugKit.Toolbar();
	DebugKit.NeatArray();
});


/**
 * Namespace
 *
 * @var object
**/
var DebugKit = {};

/**
 * Create all behaviors for neat array elements
 *
 * @var function
 */
DebugKit.NeatArray = function() {
	$$('.neat-array > li > ul').invoke('hide');

	$$('.neat-array > li').each(function(s) {
		var subList = s.select('ul');
		if (subList.size()) {
			s.addClassName('expandable').addClassName('collapsed');
			
			s.observe('click', function(evt) {
				s.toggleClassName('expanded').toggleClassName('collapsed');
				subList.invoke('toggleClassName', 'expanded').invoke('toggleClassName', 'collapsed').invoke('toggle');
			});
		}		
	})
}

/**
 * Create all behaviors for neat array elements
 *
 * @var function
 */
DebugKit.Toolbar = function() {
	var tabCollection = $$('#debug-kit-toolbar li > div');
	
	$$('#debug-kit-toolbar .panel-tab a').each(function(element) {
		element.observe('click', function(evt) {
			evt.stop();
			var targetPanel = element.hash + '-tab';

			var targetElement = $(targetPanel.sub('#', ''));
			if (!targetElement) return;
			
			if (targetElement.hasClassName('active')) {
				targetElement.removeClassName('active').setStyle({display: ''});
			}
			else {
				tabCollection.invoke('removeClassName', 'active').invoke('setStyle', {display: ''});
				targetElement.addClassName('active').setStyle({display: 'block'});
			}
		});
	});
	
	//enable hiding of toolbar.
	var panelButtons = $$('#debug-kit-toolbar .panel-tab:not(.panel-tab.icon)');
	$$('#debug-kit-toolbar #hide-toolbar').each(function(elem) {
		elem.observe('click', function(evt) {
			panelButtons.invoke('toggle');
		});
	});
}
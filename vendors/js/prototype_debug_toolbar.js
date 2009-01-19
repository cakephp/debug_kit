/* SVN FILE: $Id$ */
/**
 * Debug Toolbar Javascript.  Prototype 1.6.x compatible.
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

document.observe('dom:loaded', function() {
	new DebugKit();
});

var DebugKit = Class.create({
  
  initialize: function(){
    this.toolbar();
    this.neatArray();
  },
  
  toolbar: function(){
  	var tabCollection = $('debug-kit-toolbar').select('li > div');
	 
  	$('debug-kit-toolbar').select('.panel-tab > a').invoke('observe', 'click', function(e){
  			e.stop();
  			var targetPanel = $(e.element().hash.replace(/#/, '') + '-tab');
  			if (targetPanel.hasClassName('active')) {
  				tabCollection.each(function(ele){
				    ele.hide().removeClassName('active');
  				});
  			} else {
  				tabCollection.each(function(ele){
  				  ele.hide().removeClassName('active');
  				  if (targetPanel.id == ele.id) {
				      ele.setStyle({display: 'block'}).addClassName('active');
				    }
  				});
  			}
  			$('debug-kit-toolbar').select('.panel-tab > a').invoke('removeClassName', 'active');
  			e.element().addClassName('active');
  	});
	
  	// enable hiding of toolbar.
  	var panelButtons = $('debug-kit-toolbar').select('.panel-tab');
  	$('hide-toolbar').observe('click', function(eve){
  	  eve.stop();
  	  panelButtons.each(function(panel){
  	    if (!panel.hasClassName('icon')) {
  	      panel.toggle();
  	    };
  	  });
  	});
  },
  
/**
 * Create all behaviors for neat array elements
 */
  neatArray: function() {
    $('debug-kit-toolbar').select('.neat-array li').each(function(ele){
      var sub = ele.select('ul');
      if (sub.length > 0) {
        ele.addClassName('collapsed').addClassName('expandable');
        sub.invoke('hide');
        ele.observe('click', function(eve){
          if (eve.element() == ele || eve.element().up() == ele) {
            if (sub.length > 0) {
              ele.toggleClassName('expanded').toggleClassName('collapsed');
              sub[0].toggle();
            } 
          }
        });
      };
    });
  }
  
});

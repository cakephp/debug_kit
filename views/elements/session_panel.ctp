<?php
/**
 * Session Panel Element
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @since         DebugKit 1.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<h2><?php __d('debug_kit', 'Session'); ?></h2>
<?php echo $toolbar->makeNeatArray($content); ?>

<script type="text/javascript">

handler_url = '<?php echo Router::url(array('plugin'=>'debug_kit', 'controller'=>'toolbar_access', 'action'=>'session_value'));?>';

DEBUGKIT.module('sessionPanel');
DEBUGKIT.sessionPanel = function () {
	var Element = DEBUGKIT.Util.Element,
		Event = DEBUGKIT.Util.Event,
        Request = DEBUGKIT.Util.Request;

	return {
        handler : function(event) {
            event.preventDefault();
            event.stopPropagation();

            var elm = event.target;
            var origkey = elm.innerHTML;
            var next = elm.nextSibling;
            var val = next.innerHTML;
            var newval = prompt('New value:', val);
            if(newval && newval != val) {
                var matched = [], cur = elm.parentNode;
                while(!Element.hasClass(cur, 'depth-0')) {
                    if(Element.hasClass(cur, 'expandable')) {
                        matched.push(cur);
                    }
                    cur = cur.parentNode;
                }

                var keys = [];
                for(var i = matched.length - 1; i >= 0 ; i--) {
                    var key = matched[i].innerHTML.match(/<strong>(.*?)<\/strong>/)[1];
                    keys.push(key);
                }
                var fullkey = keys.join('.') + '.' + origkey;
                var remote = new Request({
                    method: 'post',
                    onComplete : function() {
                        next.innerHTML = newval;
                    },
                    onFail : function () {
                        alert('Session value set failed');
                    }
                });
                remote.send(handler_url + '/' + fullkey + '/' + newval);
            }
        },
		init : function () {
			var sqlPanel = document.getElementById('session-tab');
			var items = sqlPanel.getElementsByTagName('li');

			for (var i = 0; i < items.length; i++) {
				var item = items[i];
                if(!Element.hasClass(item, 'expandable')) {
                    var content = item.innerHTML;
                    var key = content.match(/<strong>(.*)<\/strong>/)[1];
                    var val = content.match(/<\/strong>(.*)$/)[1];
                    content = '<a href="#" class="edit-value">' + key + '</a>'
                                    + '<span class="session-value">' + val + '</span>';
                    item.innerHTML = content;

                    var link = item.getElementsByTagName('a')[0];
                    Event.addEvent(link, 'click', this.handler);
                }
			}
		}
	};
}();
DEBUGKIT.loader.register(DEBUGKIT.sessionPanel);
</script>
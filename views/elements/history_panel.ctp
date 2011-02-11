<?php
/**
 * View Variables Panel Element
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @since         DebugKit 1.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<h2> <?php echo __d('debug_kit', 'Request History'); ?></h2>
<?php if (empty($content)): ?>
  <p class="warning"><?php echo __d('debug_kit', 'No previous requests logged.'); ?></p>
<?php else: ?>
	<?php echo count($content); ?> <?php echo __d('debug_kit', 'previous requests available') ?>
	<ul class="history-list">
		<li><?php echo $this->Html->link(__d('debug_kit', 'Restore to current request'),
			'#', array('class' => 'history-link', 'id' => 'history-restore-current')); ?>
		</li>
		<?php foreach ($content as $previous): ?>
			<li><?php echo $this->Html->link($previous['title'], $previous['url'], array('class' => 'history-link')); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<script type="text/javascript">
//<![CDATA[
DEBUGKIT.module('historyPanel');
DEBUGKIT.historyPanel = function () {
	var toolbar = DEBUGKIT.toolbar,
		Element = DEBUGKIT.Util.Element,
		Cookie = DEBUGKIT.Util.Cookie,
		Event = DEBUGKIT.Util.Event,
		Request = DEBUGKIT.Util.Request,
		Collection = DEBUGKIT.Util.Collection,
		historyLinks = [];

	// Private methods to handle JSON response and insertion of
	// new content.
	var switchHistory = function (response) {
		try {
			var responseJson = eval( '(' + response.response.text + ')');
		} catch (e) {
			alert('Could not convert JSON response');
			return false;
		}

		Element.removeClass(historyLinks, 'loading');

		Collection.apply(toolbar.panels, function (panel, id) {
			if (panel.content === undefined || responseJson[id] === undefined) {
				return;
			}

			var panelDivs = panel.content.childNodes,
				i = panelDivs.length,
				regionDiv;

			while (i--) {
				var panelRegion = panelDivs[i];
				if (panelRegion.nodeType != 1) {
					continue;
				}
				if (
					Element.nodeName(panelRegion, 'DIV') &&
					Element.hasClass(panelRegion, 'panel-resize-region')
				) {
					regionDiv = panelRegion;
					break;
				}
			}
			if (!regionDiv) return;

			var regionDivs = regionDiv.childNodes,
				i = regionDivs.length;

			while (i--) {
				//toggle history element, hide current request one.
				var panelContent = regionDivs[i];
				if (Element.nodeName(panelContent, 'DIV') && Element.hasClass(panelContent, 'panel-history')) {
					var panelId = panelContent.id.replace('-history', '');
					if (responseJson[panelId]) {
						panelContent.innerHTML = responseJson[panelId];
						var lists;
						if (panelContent.getElementsByClassName) {
							lists = panelContent.getElementsByClassName('depth-0');
						} else {
							lists = panelContent.getElementsByTagName('UL');
						}
						toolbar.makeNeatArray(lists);
					}
					Element.show(panelContent);
				} else if (Element.nodeName(panelContent, 'DIV')) {
					Element.hide(panelContent);
				}
			}
		});
	};

	// Private method to handle restoration to current request.
	var restoreCurrentState = function () {
		var id, i, panelContent, tag;

		Element.removeClass(historyLinks, 'loading');

		//for (id in toolbar.panels) {
		Collection.apply(toolbar.panels, function (panel, id) {
			if (panel.content === undefined) {
				return;
			}

			var panelDivs = panel.content.childNodes,
				i = panelDivs.length,
				regionDiv;

			while (i--) {
				var panelRegion = panelDivs[i];
				if (panelRegion.nodeType != 1) {
					continue;
				}
				if (
					Element.nodeName(panelRegion, 'DIV') &&
					Element.hasClass(panelRegion, 'panel-resize-region')
				) {
					regionDiv = panelRegion;
					break;
				}
			}
			if (!regionDiv) return;

			var regionDivs = regionDiv.childNodes,
				i = regionDivs.length;

			while (i--) {
				panelContent = regionDivs[i];
				if (Element.nodeName(panelContent, 'DIV') && Element.hasClass(panelContent, 'panel-history')) {
					Element.hide(panelContent);
				} else if (Element.nodeName(panelContent, 'DIV')) {
					Element.show(panelContent);
				}
			}
		});
	};

	function handleHistoryLink (event) {
		event.preventDefault();

		Element.removeClass(historyLinks, 'active');
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

	return {
		init : function () {
			if (toolbar.panels['history'] === undefined) {
				console.log('bailing on history');
				return;
			}

			var anchors = toolbar.panels['history'].content.getElementsByTagName('A');
			Collection.apply(anchors, function (button) {
				if (Element.hasClass(button, 'history-link')) {
					historyLinks.push(button);
					Event.addEvent(button, 'click', handleHistoryLink);
				}
			});
		}
	};
}();

DEBUGKIT.loader.register(DEBUGKIT.historyPanel);
//]]>
</script>
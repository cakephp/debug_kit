<?php
/**
 * View Variables Panel Element
 *
 * PHP versions 5
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
		$ = DEBUGKIT.$,
		historyLinks;

	// Private methods to handle JSON response and insertion of
	// new content.
	var switchHistory = function (response) {

		historyLinks.removeClass('loading');

		$.each(toolbar.panels, function (id, panel) {
			if (panel.content === undefined || response[id] === undefined) {
				return;
			}

			var regionDiv = panel.content.find('.panel-resize-region');
			if (!regionDiv.length) {
				return;
			}

			var regionDivs = regionDiv.children();

			regionDivs.filter('div').hide();
			regionDivs.filter('.panel-history').each(function (i, panelContent) {
				var panelId = panelContent.id.replace('-history', '');
				if (response[panelId]) {
					panelContent = $(panelContent);
					panelContent.html(response[panelId]);
					var lists = panelContent.find('.depth-0');
					toolbar.makeNeatArray(lists);
				}
				panelContent.show();
			});
		});
	};

	// Private method to handle restoration to current request.
	var restoreCurrentState = function () {
		var id, i, panelContent, tag;

		historyLinks.removeClass('loading');

		$.each(toolbar.panels, function (panel, id) {
			if (panel.content === undefined) {
				return;
			}
			var regionDiv = panel.content.find('.panel-resize-region');
			if (!regionDiv.length) {
				return;
			}
			var regionDivs = regionDiv.children();
			regionDivs.filter('div').show()
				.end()
				.filter('.panel-history').hide()
		});
	};

	function handleHistoryLink (event) {
		event.preventDefault();

		historyLinks.removeClass('active');
		$(this).addClass('active loading');

		if (this.id === 'history-restore-current') {
			restoreCurrentState();
			return false;
		}

		var xhr = $.ajax({
			url: this.href,
			type: 'GET',
			dataType: 'json'
		});
		xhr.success(switchHistory).fail(function () {
			alert('History retrieval failed');
		});
	};

	return {
		init : function () {
			if (toolbar.panels['history'] === undefined) {
				console.error('Bailing on history');
				return;
			}

			historyLinks = toolbar.panels.history.content.find('.history-link');
			historyLinks.on('click', handleHistoryLink);
		}
	};
}();

DEBUGKIT.loader.register(DEBUGKIT.historyPanel);
//]]>
</script>

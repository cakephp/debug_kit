<?php
/**
 * SQL Log Panel Element
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
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/
?>
<h2><?php __d('debug_kit', 'Sql Logs')?></h2>
<?php if (!empty($content)) : ?>
	<?php foreach ($content as $dbName => $queryLog) : ?>
	<div class="sql-log-panel-query-log">
		<h4><?php echo $dbName ?></h4>
		<?php
			$headers = array('Nr', 'Query', 'Error', 'Affected', 'Num. rows', 'Took (ms)');
			echo $toolbar->table(h($queryLog['queries']), $headers, array('title' => 'SQL Log ' . $dbName));

			if (!empty($queryLog['explains'])):
				$name = sprintf(__d('debug_kit', 'toggle (%s) query explains for %s', true), count($queryLog['explains']), $dbName);
				echo $html->link($name, '#', array('class' => 'show-slow'));

				echo '<div class="slow-query-container">';
					foreach ($queryLog['explains'] as $explain):
						echo $toolbar->message(__d('debug_kit', 'Query:', true), $explain['query']);
						$headers = array_shift($explain['explain']);
						echo $toolbar->table(h($explain['explain']), $headers);
					endforeach;
				echo '</div>';
			else:
				echo $toolbar->message('Warning', __d('debug_kit', 'No slow queries!, or your database does not support EXPLAIN', true));
			endif; ?>
	</div>
	<?php endforeach; ?>
<?php else:
	echo $toolbar->message('Warning', __d('debug_kit', 'No active database connections', true));
endif; ?>

<script type="text/javascript">
//<![CDATA[
DEBUGKIT.module('sqlLog');
DEBUGKIT.sqlLog = function () {
	var Element = DEBUGKIT.Util.Element,
		Event = DEBUGKIT.Util.Event;

	return {
		init : function () {
			var sqlPanel = document.getElementById('sql_log-tab');
			var buttons = sqlPanel.getElementsByTagName('A');

			for (var i in buttons) {
				var button = buttons[i];
				if (Element.hasClass(button, 'show-slow')) {
					Event.addEvent(button, 'click', function (event) {
						event.preventDefault();
						Element.toggle(this.nextSibling);
					});
					Element.hide(button.nextSibling);
				}
			}

		}
	};
}();
DEBUGKIT.loader.register(DEBUGKIT.sqlLog);
//]]>
</script>
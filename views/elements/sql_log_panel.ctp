<?php
/**
 * SQL Log Panel Element
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
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
$headers = array('Query', 'Error', 'Affected', 'Num. rows', 'Took (ms)', 'Actions');
if (isset($debugKitInHistoryMode)) {
	$content = $toolbar->readCache('sql_log', $this->params['pass'][0]);
}
?>
<h2><?php __d('debug_kit', 'Sql Logs')?></h2>
<?php if (!empty($content)) : ?>
	<?php foreach ($content['connections'] as $dbName => $explain): ?>
	<div class="sql-log-panel-query-log">
		<h4><?php echo $dbName ?></h4>
		<?php
			if (!isset($debugKitInHistoryMode)):
				$queryLog = $toolbar->getQueryLogs($dbName, array(
					'explain' => $explain, 'threshold' => $content['threshold']
				));
			else:
				$queryLog = $content[$dbName];
			endif;
			echo $toolbar->table($queryLog, $headers, array('title' => 'SQL Log ' . $dbName));
		 ?>
		<h4><?php __d('debug_kit', 'Query Explain:'); ?></h4>
		<div id="sql-log-explain-query">
			<a id="debug-kit-explain-<?php echo $dbName ?>"> </a>
			<p><?php __d('debug_kit', 'Click an "Explain" link above, to see the query explanation.'); ?></p>
		</div>
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
		Request = DEBUGKIT.Util.Request,
		Event = DEBUGKIT.Util.Event,
		Collection = DEBUGKIT.Util.Collection;

	return {
		init : function () {
			var sqlPanel = document.getElementById('sql_log-tab');
			var buttons = sqlPanel.getElementsByTagName('input');

			// Button handling code for explain links.
			// performs XHR request to get explain query.
			var handleButton = function (event) {
				event.preventDefault();
				var data = {};
				var inputs = this.form.getElementsByTagName('input');
				var i = inputs.length;
				while (i--) {
					var input = inputs[i];
					if (input.name) {
						data[input.name] = input.value;
					}
				}

				var fetch = new Request({
					method: 'POST',
					onComplete : function (response) {
						var targetEl = document.getElementById('sql-log-explain-query');
						targetEl.innerHTML = response.response.text;
					},
					onFail : function () {
						alert('Could not fetch EXPLAIN for query.');
					}
				}).send(this.form.action, data);
			};
	
			Collection.apply(buttons, function (button) {
				if (Element.hasClass(button, 'sql-explain-link')) {
					Event.addEvent(button, 'click', handleButton);
				}
			});
		}
	};
}();
DEBUGKIT.loader.register(DEBUGKIT.sqlLog);
//]]>
</script>
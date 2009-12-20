<table class="sql-log-query-explain debug-table">
<?php
$headers = array_shift($result);

echo $html->tableHeaders($headers);
echo $html->tableCells($result);
?>
</table>
<?php Configure::write('debug', 0); ?>
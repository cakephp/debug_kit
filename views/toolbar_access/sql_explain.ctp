<table class="sql-log-query-explain">
<?php
$headers = array_keys($result[0][0]);

$rows = array();
foreach ($result as $row) {
	$rows[] = $row[0];
}
echo $html->tableHeaders($headers);
echo $html->tableCells($rows);
?>
</table>
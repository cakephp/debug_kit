<h2><?php echo __('PHP Environment');?></h2>
<?php
	$headers = array('Environment Variable', 'Value');

	if (!empty($content['php'])) {
		$phpRows = array();
		foreach ($content['php'] as $key => $val) {
			$phpRows[] = array(
				Inflector::humanize(strtolower($key)),
				$val
			);
		}
		echo $this->Toolbar->table($phpRows, $headers, array('title' => 'CakePHP Environment Vars'));
	} else {
		echo "PHP environment unavailable.";
	}
?>
<h2><?php echo __('CakePHP Environment'); ?></h2>
<?php
	if (!empty($content['cake'])) {
		$cakeRows = array();
		foreach ($content['cake'] as $key => $val) {
			$cakeRows[] = array(
				$key,
				$val
			);
		}
		echo $this->Toolbar->table($cakeRows, $headers, array('title' => 'CakePHP Environment Vars'));
	} else {
		echo "CakePHP environment unavailable.";
	}

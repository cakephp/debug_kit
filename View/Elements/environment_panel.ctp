<h2><?php echo __d('debug_kit', 'App Constants'); ?></h2>
<?php
	if (!empty($content['app'])) {
		$cakeRows = array();
		foreach ($content['app'] as $key => $val) {
			$cakeRows[] = array(
				$key,
				$val
			);
		}
		$headers = array('Constant', 'Value');
		echo $this->Toolbar->table($cakeRows, $headers, array('title' => 'Application Environment Vars'));
	} else {
		echo "No application environment available.";
	} ?>

<h2><?php echo __d('debug_kit', 'CakePHP Constants'); ?></h2>
<?php
	if (!empty($content['cake'])) {
		$cakeRows = array();
		foreach ($content['cake'] as $key => $val) {
			$cakeRows[] = array(
				$key,
				$val
			);
		}
		$headers = array('Constant', 'Value');
		echo $this->Toolbar->table($cakeRows, $headers, array('title' => 'CakePHP Environment Vars'));
	} else {
		echo "CakePHP environment unavailable.";
	} ?>

<h2><?php echo __d('debug_kit', 'PHP Environment');?></h2>
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

	if (isset($content['hidef'])) {
		echo '<h2>' . __('Hidef Environment') . '</h2>';
		if (!empty($content['hidef'])) {
			$cakeRows = array();
			foreach ($content['hidef'] as $key => $val) {
				$cakeRows[] = array(
					$key,
					$val
				);
			}
			$headers = array('Constant', 'Value');
			echo $this->Toolbar->table($cakeRows, $headers, array('title' => 'Hidef Environment Vars'));
		} else {
			echo "No Hidef environment available.";
		}
	}

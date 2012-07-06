<?php
/**
 * Evironment Element
 *
 * PHP versions 5
 *
 * Copyright 2012 Scott Harwell
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Scott Harwell 
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 **/

?>
<h2><?php echo __('CakePHP Environment');?></h2>
<?php
	$cakeHeaders = array();
	$cakeRows = array();
	foreach($content['cake'] as $key => $val){
		$cakeRows[] = array(
			Inflector::humanize(strtolower($key)),
			$val);
	}
	
	echo $this->Toolbar->table($cakeRows, $cakeHeaders, array('title' => 'CakePHP Environment Vars'));
?>

<h2><?php echo __('PHP Environment');?></h2>
<?php
	$phpHeaders = array();
	$phpRows = array();
	foreach($content['php'] as $key => $val){
		$phpRows[] = array(
			Inflector::humanize(strtolower($key)),
			$val);
	}
	
	echo $this->Toolbar->table($phpRows, $phpHeaders, array('title' => 'CakePHP Environment Vars'));
?>
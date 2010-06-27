<?php
/**
 * Request Panel Element
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
 **/
?>
<h2> <?php __d('debug_kit', 'Request'); ?></h2>
<h4>Cake Params</h4>
<?php echo $toolbar->makeNeatArray($content['params']); ?>

<h4>$_GET</h4>
<?php echo $toolbar->makeNeatArray($content['get']); ?>

<h4>Cookie</h4>
<?php if (isset($content['cookie'])): ?>
	<?php echo $toolbar->makeNeatArray($content['cookie']); ?>
<?php else: ?>
	<p class="warning">To view Cookies, add CookieComponent to Controller</p>
<?php endif; ?>

<h4><?php __d('debug_kit', 'Current Route') ?></h4>
<?php echo $toolbar->makeNeatArray($content['currentRoute']); ?>
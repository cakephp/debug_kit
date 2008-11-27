<?php
/* SVN FILE: $Id$ */
/**
 * Request Panel Element
 *
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2006-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2006-2008, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.debug_kit.views.elements
 * @since         
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<h2> <?php __('Request'); ?></h2>
<h4>Cake Params</h4>
<?php echo $toolbar->makeNeatArray($content['params']); ?>

<h4>$_GET</h4>
<?php echo $toolbar->makeNeatArray($content['get']); ?>

<h4>Cookie</h4>
<?php if (isset($content['cookie'])): ?>
	<?php echo $toolbar->makeNeatArray($content['cookie']); ?>
<?php else: ?>
	<p class="warning">To view Cookies, add CookieComponent to Controller
<?php endif; ?>

<h4><?php __('Current Route') ?></h4>
<?php echo $toolbar->makeNeatArray($content['currentRoute']); ?>
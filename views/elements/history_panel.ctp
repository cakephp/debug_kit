<?php
/* SVN FILE: $Id$ */
/**
 * View Variables Panel Element
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
<h2> <?php __('Request History'); ?></h2>
<?php $history = $this->viewVars['debugToolbarPanelsHistory']; ?>
<?php if (empty($history)) :?>
  <p class="warning"><?php __('No previous requests logged.'); ?></p>
<?php else: ?>
  <?php echo count($history); ?> <?php __('previous requests available') ?>
  <?php
    for($i = 0; $i <= count($history); $i++):
      if($i == 0) {
        $title = '(' . __('current', true) . ') ' . $this->here;
      } else {
        $title = $i;
        if(!empty($history[$i]['request'])) {
          $title = $history[$i]['request']['content']['params']['url']['url'];
        }        
      }
  ?>
    <div><?php echo $html->link($title, '#' . $i, array('class' => 'history-link')); ?></div>
  <?php endfor ?>
<?php endif; ?>

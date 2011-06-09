<?php
/**
 * View Variables Panel Element
 *
 * PHP versions 5
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
<h2> <?php echo __d('debug_kit', 'View Variables'); ?></h2>
<?php 
$content['$this->validationErrors'] = $this->validationErrors;
$content['Loaded Helpers'] = $this->Helpers->attached();
echo $this->Toolbar->makeNeatArray($content); ?>
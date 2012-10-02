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

$this->start('panelContent');
?>
<h2> <?php echo __d('debug_kit', 'View Variables'); ?></h2>
<?php
$global['title_for_layout'] = $content['title_for_layout'];
$global['$request->data'] = $content['$request->data'];
unset($content['title_for_layout'], $content['$request->data']);
echo $this->Toolbar->makeNeatArray($content);

$global['$this->validationErrors'] = $this->validationErrors;
$global['Loaded Helpers'] = $this->Helpers->attached();
echo $this->Toolbar->makeNeatArray($global);

$this->end('panelContent');
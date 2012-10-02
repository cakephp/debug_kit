<?php
/**
 * View Variables Panel Element
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

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

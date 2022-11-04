<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $paths
 * @var array $app
 * @var array $cake
 * @var array $plugins
 * @var array $vendor
 * @var array $other
 */

// Backwards compat for old DebugKit data.
if (!isset($cake) && isset($core)) {
    $cake = $core;
}
?>
<div class="c-include-panel">
    <h4>Include Paths</h4>
    <?= $this->Toolbar->dump($paths) ?>

    <h4>Included Files</h4>
    <?= $this->Toolbar->dump(compact('app', 'cake', 'plugins', 'vendor', 'other')) ?>
</div>

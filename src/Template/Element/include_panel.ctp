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
 * @var string $cake
 * @var string $core
 * @var array $paths
 * @var string $app
 * @var string $plugins
 */

// Backwards compat for old DebugKit data.
if (!isset($cake) && isset($core)) {
    $cake = $core;
}
?>
<h4><?= __d('debug_kit', 'Include Paths') ?></h4>
<?= $this->Toolbar->makeNeatArray($paths) ?>

<h4><?= __d('debug_kit', 'Included Files') ?></h4>
<?= $this->Toolbar->makeNeatArray(['cake' => $cake, 'app' => $app, 'plugins' => $plugins]) ?>

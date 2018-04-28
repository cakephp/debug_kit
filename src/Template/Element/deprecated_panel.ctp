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
use Cake\Utility\Hash;

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

$printer = function ($section, $data) {
?>
    <h3><?= h(ucfirst($section)) ?> </h3>
    <?php foreach ($data as $group => $groupData): ?>
        <?php foreach ($groupData as $file => $messages): ?>
            <h4><?= h($group) ?> : <?= h($file) ?></h4>
            <ul class="list deprecation-list">
                <li><?= implode('</li><li>', h($messages)) ?></li>
            </ul>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php
};
?>

<?php
if (count($app)):
    $printer('app', $app);
endif;

if (count($plugins)):
    foreach ($plugins as $plugin => $pluginData):
        $printer($plugin, $pluginData);
    endforeach;
endif;

if (count($cake)):
    $printer('cake', $cake);
endif;

if (count($vendor)):
    $printer('vendor', $vendor);
endif;

if (count($other)):
    $printer('other', $other);
endif;

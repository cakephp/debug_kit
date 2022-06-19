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
 * @since         DebugKit 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * @var \DebugKit\View\AjaxView $this
 * @var string $error
 * @var bool $sort
 * @var array $variables
 * @var array $content
 * @var array $errors
 */
?>
<div class="c-variables-panel">
    <?php
    if (isset($error)) :
        printf('<p class="c-flash c-flash--warning">%s</p>', $error);
    endif;

    if (isset($varsMaxDepth)) {
        $msg = sprintf(__d('debug_kit', '%s levels of nested data shown.'), $varsMaxDepth);
        $msg .= ' ' . __d('debug_kit', 'You can overwrite this via the config key');
        $msg .= ' <strong>DebugKit.variablesPanelMaxDepth</strong><br>';
        $msg .= __d('debug_kit', 'Increasing the depth value can lead to an out of memory error.');
        printf('<p class="c-flash c-flash--info">%s</p>', $msg);
    }

    // Backwards compatibility for old debug kit data.
    if (!empty($content)) :?>
        <div class="o-checkbox">
            <label>
                <input
                    type="checkbox"
                    class="js-debugkit-sort-variables"
                    <?= $sort ? ' checked="checked"' : '' ?>>
                    <?= __d('debug_kit', 'Sort variables by name') ?>
            </label>
        </div>
        <?php
        $this->Toolbar->setSort($sort);
        echo $this->Toolbar->dump($content);
    endif;

    // New node based data.
    if (!empty($variables)) :?>
        <div class="o-checkbox">
            <label>
                <input
                    type="checkbox"
                    class="js-debugkit-sort-variables"
                    <?= $sort ? ' checked="checked"' : '' ?>>
                <?= __d('debug_kit', 'Sort variables by name') ?>
            </label>
        </div>
        <?php
        $this->Toolbar->setSort($sort);
        echo $this->Toolbar->dumpNodes($variables);
    endif;

    if (!empty($errors)) :
        echo '<h4>' . __d('debug_kit', 'Validation errors') . '</h4>';
        echo $this->Toolbar->dump($errors);
    endif;
    ?>
</div>

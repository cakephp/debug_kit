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
 * @var array $content
 * @var array $errors
 */

if (isset($error)):
    printf('<p class="warning">%s</p>', $error);
endif;

if (!empty($content)):
    printf('<label class="toggle-checkbox"><input type="checkbox" class="neat-array-sort"%s>%s</label>', $sort ? ' checked="checked"' : '', __d('debug_kit', 'Sort variables by name'));
    $this->Toolbar->setSort($sort);
    echo $this->Toolbar->makeNeatArray($content);
endif;

if (!empty($errors)):
    echo '<h4>Validation errors</h4>';
    echo $this->Toolbar->makeNeatArray($errors);
endif;

<?php
/**
 * Request Panel Element
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

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $headers
 * @var array $attributes
 * @var array $data
 * @var array $query
 * @var array $cookie
 * @var string $matchedRoute
 */
?>
<div class="c-request-panel">
    <?php if (!empty($headers) && $headers['response']) : ?>
        <h4>Warning</h4>
        <p class="c-flash c-flash--warning">
            <?= sprintf(
                'Headers already sent at file %s and line %d.',
                $headers['file'],
                $headers['line']
            ) ?>
        </p>
    <?php endif; ?>

    <h4>Attributes</h4>
    <?php
    if (empty($attributes)) :
        echo '<p class="c-flash c-flash--info">No attributes data.</p>';
    else :
        echo $this->Toolbar->dump($attributes);
    endif;
    ?>

    <h4>Post data</h4>
    <?php
    if (empty($data)) :
        echo '<p class="c-flash c-flash--info">No post data.</p>';
    else :
        echo $this->Toolbar->dump($data);
    endif;
    ?>

    <h4>Query string</h4>
    <?php
    if (empty($query)) :
        echo '<p class="c-flash c-flash--info">No querystring data.</p>';
    else :
        echo $this->Toolbar->dump($query);
    endif;
    ?>

    <h4>Cookie</h4>
    <?php if (isset($cookie)) : ?>
        <?= $this->Toolbar->dump($cookie) ?>
    <?php else : ?>
        <p class="c-flash c-flash--info">No Cookie data.</p>
    <?php endif; ?>

    <?php if (!empty($matchedRoute)) : ?>
    <h4>Matched Route</h4>
        <p><?= $this->Toolbar->dump(['template' => $matchedRoute]) ?></p>
    <?php endif; ?>
</div>

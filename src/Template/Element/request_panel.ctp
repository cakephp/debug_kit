<?php
/**
 * Request Panel Element
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         DebugKit 0.1
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * @var \DebugKit\View\AjaxView $this
 * @var array $headers
 * @var array $params
 * @var array $data
 * @var array $query
 * @var array $cookie
 * @var string $matchedRoute
 */
?>
<?php if (!empty($headers) && $headers['response']): ?>
<h4><?= __d('debug_kit', 'Warning') ?></h4>
    <?= '<p class="warning">' . __d('debug_kit', 'Headers already sent at file {0} and line {1}.', [$headers['file'], $headers['line']]) . '</p>' ?>
<?php endif; ?>

<h4><?= __d('debug_kit', 'Routing Params') ?></h4>
<?= $this->Toolbar->makeNeatArray($params) ?>

<h4><?= __d('debug_kit', 'Post data') ?></h4>
<?php
if (empty($data)):
    echo '<p class="info">' . __d('debug_kit', 'No post data.') . '</p>';
else:
    echo $this->Toolbar->makeNeatArray($data);
endif;
?>

<h4>Query string</h4>
<?php
if (empty($query)):
    echo '<p class="info">' . __d('debug_kit', 'No querystring data.') . '</p>';
else:
    echo $this->Toolbar->makeNeatArray($query);
endif;
?>

<h4>Cookie</h4>
<?php if (isset($cookie)): ?>
    <?= $this->Toolbar->makeNeatArray($cookie) ?>
<?php else: ?>
    <p class="info"><?= __d('debug_kit', 'No Cookie data.') ?></p>
<?php endif; ?>

<?php if (!empty($matchedRoute)): ?>
<h4><?= __d('debug_kit', 'Matched Route') ?></h4>
    <p><?= $this->Toolbar->makeNeatArray(['template' => $matchedRoute]) ?></p>
<?php endif; ?>

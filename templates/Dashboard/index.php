<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h1><?= __d('debug_kit', 'Debug Kit Dashboard') ?></h1>

<h2><?= __d('debug_kit', 'Database') ?></h2>
<ul>
    <li><?= __d('debug_kit', 'Driver') ?>: <?= h($connection['driver']); ?></li>
    <?php if (isset($connection['rows'])): ?>
    <li><?= __d('debug_kit', 'Requests') ?>: <?= $this->Number->format($connection['rows']) ?></li>
    <?php endif; ?>
</ul>
<?php if (!empty($connection['rows'])): ?>
    <?= $this->Form->postLink(
        __d('debug_kit', 'Reset database'),
        ['_method' => 'POST', 'action' => 'reset'],
        ['confirm' => 'Are you sure?']
    ); ?>
<?php endif; ?>

<h3>Actions</h3>
<ul>
    <li><?= $this->Html->link(__d('debug_kit', 'Mail Preview'), ['controller' => 'MailPreview']); ?></li>
</ul>

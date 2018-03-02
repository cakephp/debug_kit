<?php
/**
 * @var \App\View\AppView $this
 */
?>
<h1>Debug Kit</h1>

<h2>Database</h2>
<ul>
    <li><?= h($connection['driver']); ?></li>
    <?php if (isset($connection['size'])): ?>
    <li>DB Size: <?php echo $this->Number->toReadableSize($connection['size']); ?></li>
    <?php endif; ?>
</ul>
<?php if (!empty($connection['size'])): ?>
<?php echo $this->Form->postLink('Reset', ['action' => 'reset'], ['confirm' => 'Sure?']); ?>
<?php endif; ?>

<h3>Actions</h3>
<ul>
    <li><?php echo $this->Html->link('Mail Preview', ['controller' => 'MailPreview']); ?></li>
</ul>

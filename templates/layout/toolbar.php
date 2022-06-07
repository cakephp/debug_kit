<?php
/**
 * @var \DebugKit\View\AjaxView $this
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= isset($title) ? h($title) : "Debug Kit Toolbar" ?></title>
        <?= $this->Html->css('DebugKit./css/reset') ?>
        <?= $this->Html->css('DebugKit./css/toolbar') ?>
    </head>
    <body>
    <?= $this->fetch('content') ?>
    <div id="o-loader">
        <?= $this->Html->image('DebugKit./img/cake.icon.png', ['class' => 'o-loader__indicator'])?>
    </div>
    </body>
    <?= $this->fetch('script') ?>
</html>

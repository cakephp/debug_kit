<?php
use Cake\Routing\Router;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Debug Kit Toolbar</title>
        <?= $this->Html->css('DebugKit.reset') ?>
        <?= $this->Html->css('DebugKit.toolbar') ?>
    </head>
    <body>
    <?= $this->fetch('content') ?>
    <div id="loader">
        <?= $this->Html->image('DebugKit.cake.icon.png', ['class' => 'indicator'])?>
    </div>
    </body>
    <?= $this->Html->script('DebugKit.jquery') ?>
    <?= $this->Html->script('DebugKit.toolbar-app') ?>
    <?= $this->Html->script('DebugKit.debug_kit', ['id' => '__debug_kit', 'data-url' => json_encode($this->Url->build('/')), 'data-full-url' => Router::url('/', true)]) ?>
    <?= $this->fetch('scripts') ?>
</html>

<?php $this->extend('toolbar') ?>
<h2 class="panel-title"><?= isset($title) ? $title : "Mailer Previews" ?></h2>
<div class="panel-content">
    <?= $this->fetch('content'); ?>
</div>


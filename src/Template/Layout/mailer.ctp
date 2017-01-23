<?php $this->extend('toolbar') ?>

<?php if (empty($noHeader)) : ?>
    <h2 class="panel-title">
        <?= isset($title) ? $title : "Mailer Previews" ?>
    </h2>
<?php endif ?>

<div class="panel-content">
    <?= $this->fetch('content'); ?>
</div>


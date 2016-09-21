<?php
/**
 * @type \DebugKit\View\AjaxView $this
 * @type \DebugKit\Model\Entity\Panel $panel
 */
?>
<h2 class="panel-title"><?= h($panel->title) ?></h2>
<div class="panel-content">
    <?= $this->element($panel->element) ?>
</div>

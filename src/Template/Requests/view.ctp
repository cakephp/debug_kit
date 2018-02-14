<?php
/**
 * @var \DebugKit\View\AjaxView $this
 * @var \DebugKit\Model\Entity\Request $toolbar
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

?>
<div id="panel-content-container">
    <span id="panel-close" class="button-close">&times;</span>
    <div id="panel-content">
        <!-- content here -->
    </div>
</div>

<ul id="toolbar" class="toolbar">
     <li class="panel-button-left panel hidden">
        <span class="panel-button">
            &#x3008;
        </span>
    </li>
    <li class="panel-button-right panel hidden">
        <span class="panel-button">
            &#x3009;
        </span>
    </li>
    <li class="toolbar-inner">
        <ul class="toolbar-inner">
        <?php foreach ($toolbar->panels as $panel): ?>
        <li class="panel hidden" data-id="<?= $panel->id ?>">
            <span class="panel-button">
                <?= h($panel->title) ?>
            </span>
            <?php if (strlen($panel->summary)): ?>
            <span class="panel-summary">
                <?= h($panel->summary) ?>
            </span>
            <?php endif ?>
        </li>
        <?php endforeach; ?>
        </ul>
    </li>
    <li id="panel-button">
        <?= $this->Html->image('DebugKit./img/cake.icon.png', [
            'alt' => 'Debug Kit', 'title' => 'CakePHP ' . Configure::version() . ' Debug Kit'
        ]) ?>
    </li>
</ul>
<?php $this->Html->script('DebugKit./js/debug_kit', [
    'block' => true,
    'id' => '__debug_kit_app',
    'data-id' => $toolbar->id,
    'data-url' => Router::url('/', true),
    'data-webroot' => $this->request->webroot,
]) ?>

<?php
/**
 * @var \DebugKit\View\AjaxView $this
 * @var \DebugKit\Model\Entity\Request $toolbar
 */

use Cake\Routing\Router;
use Cake\Core\Configure;

?>
<div id="c-panel-content-container js-panel-content-container">
    <span class="c-panel-content-container__close js-panel-close">&times;</span>
    <div id="c-panel-content-container__content">
        <!-- content here -->
    </div>
</div>

<ul id="toolbar" class="c-toolbar js-toolbar">
     <li class="c-toolbar__scroll-button c-toolbar__scroll-button--left is-hidden">
        <span class="c-panel__button">
            &#x3008;
        </span>
    </li>
    <li class="c-toolbar__scroll-button c-toolbar__scroll-button--right is-hidden">
        <span class="c-panel__button">
            &#x3009;
        </span>
    </li>
    <li class="c-toolbar__inner">
        <ul>
        <?php foreach ($toolbar->panels as $panel) : ?>
            <li class="c-panel js-panel-button is-hidden" data-id="<?= $panel->id ?>">
                <span class="c-panel__button">
                    <?= h($panel->title) ?>
                </span>
                <?php if (!empty($panel->summary)) : ?>
                <span class="c-panel__summary">
                    <?= h($panel->summary) ?>
                </span>
                <?php endif ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </li>
    <li id="c-toolbar__button">
        <?= $this->Html->image('DebugKit./img/cake.icon.png', [
            'alt' => 'Debug Kit', 'title' => 'CakePHP ' . Configure::version() . ' Debug Kit'
        ]) ?>
    </li>
</ul>
<?php $this->Html->script('DebugKit./js/main', [
    'block' => true,
    'id' => '__debug_kit_app',
    'data-id' => $toolbar->id,
    'data-url' => Router::url('/', true),
    'data-webroot' => $this->getRequest()->getAttribute("webroot"),
]) ?>

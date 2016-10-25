<?php
/**
 * @var \Cake\Routing\Route\Route[] $routes
 * @var string $matchedRoute
 */
?>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
    <tr>
        <th><?= __d('debug_kit', 'Route name') ?></th>
        <th><?= __d('debug_kit', 'URI template') ?></th>
        <th><?= __d('debug_kit', 'Defaults') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($routes as $route): ?>
        <tr class="<?= ($matchedRoute === $route->template) ? 'highlighted' : '' ?>">
            <td><?= \Cake\Utility\Hash::get($route->options, '_name', $route->getName()) ?></td>
            <td><?= $route->template ?></td>
            <td><?= json_encode($route->defaults) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

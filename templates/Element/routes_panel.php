<?php
/**
 * @var \Cake\Routing\Route\Route[] $routes
 * @var string $matchedRoute
 */
use Cake\Utility\Hash;

$routes = Cake\Routing\Router::routes();
?>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
    <tr>
        <th><?= __d('debug_kit', 'Route name') ?></th>
        <th class="left"><?= __d('debug_kit', 'URI template') ?></th>
        <th class="left"><?= __d('debug_kit', 'Defaults') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($routes as $route): ?>
        <tr class="<?= ($matchedRoute === $route->template) ? 'highlighted' : '' ?>">
            <td><?= h(Hash::get($route->options, '_name', $route->getName())) ?></td>
            <td class="left"><?= h($route->template) ?></td>
            <td class="left"><pre><?= json_encode($route->defaults, JSON_PRETTY_PRINT) ?></pre></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

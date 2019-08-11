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
        <?php 
        $class = '';
        if ($matchedRoute === $route->template):
            $class = 'highlighted';
        elseif ($route->defaults['plugin'] === 'DebugKit'):
            $class = 'debugkit-route hidden';
        endif;
        ?>
        <tr class="<?= $class ?>">
            <td><?= h(Hash::get($route->options, '_name', $route->getName())) ?></td>
            <td class="left"><?= h($route->template) ?></td>
            <td class="left"><pre><?= json_encode($route->defaults, JSON_PRETTY_PRINT) ?></pre></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<button type="button" class="btn-primary" id="toggle-debugkit-routes">
    <?= __d('debug_kit', 'Toggle debugkit internal routes') ?>
</button>

<script>
$(document).ready(function() {
    $('#toggle-debugkit-routes').on('click', function (event) {
        event.preventDefault();
        var routes = $('.debugkit-route');
        routes.toggleClass('hidden');
    });
});
</script>

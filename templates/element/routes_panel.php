<?php
/**
 * @var \Cake\Routing\Route\Route[] $routes
 * @var string $matchedRoute
 */

use Cake\Core\Plugin as CorePlugin;
use Cake\Utility\Hash;
use Cake\Utility\Text;

$routes = Cake\Routing\Router::routes();

$amountOfRoutesPerGroup = $duplicateRoutes = [];
foreach ($routes as $route) {
    // Count the amount
    $group = $route->defaults['plugin'] ?? 'app';
    if (!array_key_exists($group, $amountOfRoutesPerGroup)) {
        $amountOfRoutesPerGroup[$group] = 0;
    }
    $amountOfRoutesPerGroup[$group]++;

    if (!array_key_exists($route->template, $duplicateRoutes)) {
        $duplicateRoutes[$route->template] = 0;
    }
    $duplicateRoutes[$route->template]++;
}

$pluginNames = [];
foreach (CorePlugin::loaded() as $pluginName) {
    if (!empty($amountOfRoutesPerGroup[$pluginName])) {
        $name = sprintf('%s (%s)', $pluginName, $amountOfRoutesPerGroup[$pluginName]);
        $pluginNames[$name] = Text::slug($pluginName);
    }
}

?>
<div class="debugkit-plugin-routes-button-wrapper">
    <button type="button" class="btn-primary js-debugkit-toggle-plugin-route" data-plugin=".route-entry--app">
        <?= __d('debug_kit', 'App') ?>
        <?= !empty($amountOfRoutesPerGroup['app']) ? ' (' . $amountOfRoutesPerGroup['app'] . ')' : '' ?>
    </button>
    <?php foreach ($pluginNames as $pluginName => $parsedName) : ?>
        <button type="button" class="btn-primary js-debugkit-toggle-plugin-route
            <?= strpos($pluginName, 'DebugKit') === 0 ? ' toggle-plugin-route-active' : '' ?>"
                data-plugin=".route-entry--plugin-<?= $parsedName ?>">
            <?= $pluginName ?>
        </button>
    <?php endforeach; ?>
</div>
<table cellspacing="0" cellpadding="0" class="debug-table">
    <thead>
    <tr>
        <th><?= __d('debug_kit', 'Route name') ?></th>
        <th class="left"><?= __d('debug_kit', 'URI template') ?></th>
        <th class="left"><?= __d('debug_kit', 'Defaults') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($routes as $route) : ?>
        <?php
        $class = '';
        if (empty($route->defaults['plugin'])) :
            $class = 'route-entry route-entry--app';
        else :
            $class = 'route-entry route-entry--plugin route-entry--plugin-' .
                Text::slug($route->defaults['plugin']);

            // Hide DebugKit internal routes by default
            if ($route->defaults['plugin'] === 'DebugKit') {
                $class .= ' hidden';
            }
        endif;

        // Highlight current route
        if ($matchedRoute === $route->template) {
            $class .= ' highlighted';
        }

        // Mark duplicate routes
        if ($duplicateRoutes[$route->template] > 1) {
            $class .= ' duplicate-route';
        }

        ?>
        <tr class="<?= $class ?>">
            <td><?= h(Hash::get($route->options, '_name', $route->getName())) ?></td>
            <td class="left"><?= h($route->template) ?></td>
            <td class="left"><pre><?= json_encode($route->defaults, JSON_PRETTY_PRINT) ?></pre></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#toggle-debugkit-routes').on('click', function (event) {
            event.preventDefault();
            var routes = $('.debugkit-route');
            routes.toggleClass('hidden');
        });

        $('.js-debugkit-toggle-plugin-route').on('click', function (event) {
            var $this = $(this);
            var plugin = $this.attr('data-plugin');

            if($this.hasClass('toggle-plugin-route-active')) {
                $this.removeClass('toggle-plugin-route-active');
                $('.route-entry' + plugin).removeClass('hidden');
            } else {
                $this.addClass('toggle-plugin-route-active');
                $('.route-entry' + plugin).addClass('hidden');
            }

        });
    });
</script>

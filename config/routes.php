<?php
use Cake\Routing\Router;

Router::plugin('DebugKit', function ($routes) {
    $routes->extensions('json');
    $routes->connect(
        '/toolbar/clear_cache',
        ['controller' => 'Toolbar', 'action' => 'clearCache']
    );
    $routes->connect(
        '/toolbar/sql_explain',
        ['controller' => 'Toolbar', 'action' => 'sqlExplain']
    );
    $routes->connect(
        '/toolbar/*',
        ['controller' => 'Requests', 'action' => 'view']
    );
    $routes->connect(
        '/panels/view/*',
        ['controller' => 'Panels', 'action' => 'view']
    );
    $routes->connect(
        '/panels/*',
        ['controller' => 'Panels', 'action' => 'index']
    );
});

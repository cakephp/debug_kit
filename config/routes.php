<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('DebugKit', ['path' => '/debug-kit'], function (RouteBuilder $routes) {
    $routes->extensions('json');
    $routes->connect(
        '/toolbar/clear-cache',
        ['controller' => 'Toolbar', 'action' => 'clearCache']
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

    $routes->connect(
        '/composer/check-dependencies',
        ['controller' => 'Composer', 'action' => 'checkDependencies']
    );

    $routes->scope(
        '/mail-preview',
        ['controller' => 'MailPreview'],
        function (RouteBuilder $routes) {
            $routes->connect('/', ['action' => 'index']);
            $routes->connect('/preview', ['action' => 'email']);
            $routes->connect('/preview/*', ['action' => 'email']);
            $routes->connect('/sent/:panel/:id', ['action' => 'sent'], ['pass' => ['panel', 'id']]);
        }
    );
});

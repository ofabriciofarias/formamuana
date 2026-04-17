<?php

// 0. Autoloader do Composer
require_once __DIR__ . '/vendor/autoload.php';

// 1. Configurações e núcleo
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mail.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/helpers.php';

// 2. Dispatcher do FastRoute
$dispatcher = FastRoute\simpleDispatcher(require __DIR__ . '/routes/web.php');

// 3. Extrair método HTTP e URI (sem query string)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// 4. Despachar
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {

    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '<h1>404 — Página não encontrada</h1>';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '<h1>405 — Método não permitido</h1>';
        break;

    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        require_once __DIR__ . '/controllers/' . $controllerClass . '.php';

        $controller = new $controllerClass();
        $controller->$method(...array_values($vars));
        break;
}

<?php

declare(strict_types=1);

use App\Presentation\Router\Request;
use App\Presentation\Router\Router;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

$router = new Router();

$router->onHttpError(function (int $code, Router $router) {
    switch ($code) {
        case 404:
            $router->response()->body(
                "Page not found"
            );
            break;
        default:
            $router->response()->body(
                "Something went wrong " . $code
            );
    }
});

$router->respond("/", function () {
    return "Hello World";
});

$router->respond("/[a:name]", function (Request $request) {
    return "Hello " . $request->name;
});

$router->dispatch();

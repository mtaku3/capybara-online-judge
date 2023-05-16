<?php

declare(strict_types=1);

use App\Application\ValidateSession\ValidateSessionRequest;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Request;
use App\Presentation\Router\Router;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

$router = new Router();
$container = $GLOBALS["container"];

$router->onHttpError(function (int $code, Router $router) use ($container) {
    switch ($code) {
        case 404:
            $router->response()->body(
                $container->get("Twig")->render("404.twig")
            );
            break;
        default:
            $router->response()->body(
                "Something went wrong " . $code
            );
    }
});

$router->respond(function (Request $req) use ($container) {
    $cookies = $req->cookies();

    if (isset($cookies["x-user-id"]) && isset($cookies["x-refresh-token"])) {
        try {
            $validateSessionResponse = $container->get("ValidateSessionUseCase")->handle(
                new ValidateSessionRequest($cookies["x-user-id"], $cookies["x-access-token"], $cookies["x-refresh-token"])
            );
            $req->user = $validateSessionResponse->User;
        } catch (Exception $e) {
            // ignored
        }
    }
});

$router->respond("/", function (Request $req, AbstractResponse $res) use ($container) {
    $container->get("ProblemListController")->get($req, $res);
});

$router->respond("/[a:name]", function (Request $request) {
    return "Hello " . $request->name;
});

$router->dispatch();

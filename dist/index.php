<?php

declare(strict_types=1);

use App\Application\ValidateSession\ValidateSessionRequest;
use App\Domain\User\ValueObject\UserId;
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

$router->respond(function (Request $req, AbstractResponse $res) use ($container) {
    $cookies = $req->cookies();

    if (isset($cookies["x-user-id"]) && isset($cookies["x-refresh-token"])) {
        try {
            /** @var \App\Application\ValidateSession\ValidateSessionResponse */
            $validateSessionResponse = $container->get("ValidateSessionUseCase")->handle(
                new ValidateSessionRequest(new UserId($cookies["x-user-id"]), $cookies["x-access-token"], $cookies["x-refresh-token"])
            );

            $res->cookie("x-user-id", (string)$validateSessionResponse->User->getId(), ($validateSessionResponse->Session ?? $validateSessionResponse->AccessToken)->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-access-token", (string)$validateSessionResponse->AccessToken, $validateSessionResponse->AccessToken->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            if (empty($validateSessionResponse->Session)) {
                $res->cookie("x-refresh-token", expiry: 1);
            } else {
                $res->cookie("x-refresh-token", (string)$validateSessionResponse->Session->getRefreshToken(), $validateSessionResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            }

            $req->user = $validateSessionResponse->User;
        } catch (Exception $e) {
            $res->cookie("x-user-id", expiry: 1);
            $res->cookie("x-access-token", expiry: 1);
            $res->cookie("x-refresh-token", expiry: 1);
        }
    }
});

$router->respond("/", function (Request $req, AbstractResponse $res) use ($container) {
    $container->get("ProblemListController")->get($req, $res);
});

$router->respond("/auth/register", function (Request $req, AbstractResponse $res) use ($container) {
    $container->get("RegisterController")->get($req, $res);
});

$router->dispatch();

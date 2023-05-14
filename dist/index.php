<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

use App\Libs\Request;
use App\Libs\Response;

$router = new \App\Libs\Router();


$router->get("/", function (Request $req, Response $res) {
    echo $GLOBALS["container"]->get("Twig")->render("index.twig");
});

$router->get("/demo/texme", function (Request $req, Response $res) {
    echo $GLOBALS["container"]->get("Twig")->render("demo/texme.twig");
});

$router->get("/demo/codemirror", function (Request $req, Response $res) {
    echo $GLOBALS["container"]->get("Twig")->render("demo/codemirror.twig");
});


if (\App\Libs\Router::GetHasRouted() === false) {
    echo $GLOBALS["container"]->get("Twig")->render("404.twig");
}

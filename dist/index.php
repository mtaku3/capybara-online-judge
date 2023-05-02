<?php require __DIR__ . '/../vendor/autoload.php';

use App\Libs\Request;
use App\Libs\Response;

$router = new \App\Libs\Router();

$router->get("/", function (Request $req, Response $res) {
    echo "Hello World!";
});

if (\App\Libs\Router::GetHasRouted() === false) {
    $res = new Response();
    $res->status(404);
    $res->send("Not Found");
}

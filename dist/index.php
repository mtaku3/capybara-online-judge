<?php require __DIR__ . '/../vendor/autoload.php';

use App\Libs\Request;
use App\Libs\Response;

$router = new \App\Libs\Router();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/../src/App/Pages");
$twig = new \Twig\Environment($loader, [
    // "cache" => __DIR__ . "/../.cache",
]);
$GLOBALS["twig"] = $twig;


$router->get("/", function (Request $req, Response $res) {
    echo $GLOBALS["twig"]->render("index.twig");
});

$router->get("/demo/texme", function (Request $req, Response $res) {
    echo $GLOBALS["twig"]->render("demo/texme.twig");
});

$router->get("/demo/codemirror", function (Request $req, Response $res) {
    echo $GLOBALS["twig"]->render("demo/codemirror.twig");
});

if (\App\Libs\Router::GetHasRouted() === false) {
    echo $twig->render("404.twig");
}

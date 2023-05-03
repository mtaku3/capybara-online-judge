<?php require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

use App\Libs\Request;
use App\Libs\Response;

$router = new \App\Libs\Router();

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/../src/App/Pages");
$options = [];
if (isset($_ENV["ENV"]) && $_ENV["ENV"] === "production") {
    $options["cache"] = __DIR__ . "/../.cache";
}
$twig = new \Twig\Environment($loader, $options);
$GLOBALS["twig"] = $twig;


$redis = new Redis();
$redis->connect($_ENV["REDIS_HOST"], $_ENV["REDIS_PORT"]);
$GLOBALS["redis"] = $redis;


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

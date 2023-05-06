<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
$dotenv->required("POSTGRES_HOST")->notEmpty();
$dotenv->required("POSTGRES_PORT")->isInteger();
$dotenv->required("POSTGRES_USER")->notEmpty();
$dotenv->required("POSTGRES_PASSWORD")->notEmpty();
$dotenv->required("POSTGRES_DB")->notEmpty();
$dotenv->required("REDIS_HOST")->notEmpty();
$dotenv->required("REDIS_PORT")->isInteger();

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
$redis->connect($_ENV["REDIS_HOST"], intval($_ENV["REDIS_PORT"]));
$GLOBALS["redis"] = $redis;


use Cycle\Database;
use Cycle\Database\Config;
use Cycle\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\Mapper\Mapper;

$dbal = new Database\DatabaseManager(
    new Config\DatabaseConfig([
        "default" => "default",
        "databases" => [
            "default" => ["connection" => "postgres"]
        ],
        "connections" => [
            'postgres' => new Config\PostgresDriverConfig(
                connection: new Config\Postgres\TcpConnectionConfig(
                    database: $_ENV["POSTGRES_DB"],
                    host: $_ENV["POSTGRES_HOST"],
                    port: intval($_ENV["POSTGRES_PORT"]),
                    user: $_ENV["POSTGRES_USER"],
                    password: $_ENV["POSTGRES_PASSWORD"]
                ),
                schema: 'public',
                queryCache: true,
            )
        ]
    ])
);
$orm = new ORM\ORM(new ORM\Factory($dbal), new Schema([]));


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

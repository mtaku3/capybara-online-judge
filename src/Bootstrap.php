<?php

declare(strict_types=1);

use Cycle\Database;
use Cycle\Database\Config;
use Cycle\ORM;
use Cycle\ORM\EntityManager;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

/** Load .env */
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
$dotenv->required("POSTGRES_HOST")->notEmpty();
$dotenv->required("POSTGRES_PORT")->isInteger();
$dotenv->required("POSTGRES_USER")->notEmpty();
$dotenv->required("POSTGRES_PASSWORD")->notEmpty();
$dotenv->required("POSTGRES_DB")->notEmpty();
$dotenv->required("REDIS_HOST")->notEmpty();
$dotenv->required("REDIS_PORT")->isInteger();
$dotenv->required("JWT_SECRET")->notEmpty();

/** Setup Dependency Injection */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    "Twig" => function () {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/../src/App/Pages");
        $options = [];
        if (isset($_ENV["ENV"]) && $_ENV["ENV"] === "production") {
            $options["cache"] = __DIR__ . "/../.cache";
        }
        return new \Twig\Environment($loader, $options);
    },
    "Redis" => function () {
        $redis = new Redis();
        $redis->connect($_ENV["REDIS_HOST"], intval($_ENV["REDIS_PORT"]));
        return $redis;
    },
    "ORM" => function () {
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

        require_once __DIR__ . "/../src/App/Infrastructure/CycleORM/Schema.php";

        return new ORM\ORM(new ORM\Factory($dbal), $Schema);
    },
    "EntityManager" => function (ContainerInterface $c) {
        return new EntityManager($c->get("ORM"));
    }
]);

$GLOBALS["container"] = $containerBuilder->build();

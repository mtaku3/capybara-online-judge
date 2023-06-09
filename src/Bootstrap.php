<?php

declare(strict_types=1);

use App\Application\AddProblemLanguages\AddProblemLanguagesUseCase;
use App\Application\Authorize\AuthorizeUseCase;
use App\Application\CreateProblem\CreateProblemUseCase;
use App\Application\CreateTestCase\CreateTestCaseUseCase;
use App\Application\CreateUser\CreateUserUseCase;
use App\Application\DeleteProblem\DeleteProblemUseCase;
use App\Application\DeleteSubmission\DeleteSubmissionUseCase;
use App\Application\DisableTestCase\DisableTestCaseUseCase;
use App\Application\EnableTestCase\EnableTestCaseUseCase;
use App\Application\GetProblemById\GetProblemByIdUseCase;
use App\Application\GetProblems\GetProblemsUseCase;
use App\Application\GetSubmissionById\GetSubmissionByIdUseCase;
use App\Application\GetSubmissionsByProblemId\GetSubmissionsByProblemIdUseCase;
use App\Application\GetSubmissionsByProblemIdAndUserId\GetSubmissionsByProblemIdAndUserIdUseCase;
use App\Application\GetSubmissionsByUserId\GetSubmissionsByUserIdUseCase;
use App\Application\RemoveProblemLanguages\RemoveProblemLanguagesUseCase;
use App\Application\Session\Entity\Session;
use App\Application\Submit\SubmitUseCase;
use App\Application\UpdateCompileRule\UpdateCompileRuleUseCase;
use App\Application\UpdateProblemTitleAndBody\UpdateProblemTitleAndBodyUseCase;
use App\Application\UpdateTestCase\UpdateTestCaseUseCase;
use App\Application\ValidateSession\ValidateSessionUseCase;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Submission\Entity\Submission;
use App\Domain\User\Entity\User;
use App\Infrastructure\Repository\File\FileRepository;
use App\Infrastructure\Repository\JudgeQueue\JudgeQueueRepository;
use App\Infrastructure\Repository\Problem\ProblemRepository;
use App\Infrastructure\Repository\Session\SessionRepository;
use App\Infrastructure\Repository\Submission\SubmissionRepository;
use App\Infrastructure\Repository\User\UserRepository;
use App\Presentation\Controller\LoginController;
use App\Presentation\Controller\ProblemController;
use App\Presentation\Controller\ProblemListController;
use App\Presentation\Controller\SubmissionController;
use App\Presentation\Controller\TestCaseController;
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

$_ENV["ISDEV"] = !isset($_ENV["ENV"]) || $_ENV["ENV"] !== "production";

/** Setup Dependency Injection */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    /** Infrastructure Layer */
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
    },
    "ProblemRepository" => function (ContainerInterface $c) {
        return new ProblemRepository($c->get("EntityManager"), $c->get("ORM")->getRepository(Problem::class));
    },
    "SessionRepository" => function (ContainerInterface $c) {
        return new SessionRepository($c->get("EntityManager"), $c->get("ORM")->getRepository(Session::class));
    },
    "SubmissionRepository" => function (ContainerInterface $c) {
        return new SubmissionRepository($c->get("EntityManager"), $c->get("ORM")->getRepository(Submission::class));
    },
    "UserRepository" => function (ContainerInterface $c) {
        return new UserRepository($c->get("EntityManager"), $c->get("ORM")->getRepository(User::class));
    },
    "JudgeQueueRepository" => function (ContainerInterface $c) {
        return new JudgeQueueRepository($c->get("Redis"));
    },
    "FileRepository" => function () {
        return new FileRepository();
    },

    /** Presentation Layer */
    "Twig" => function () {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . "/../src/App/Presentation/Template");
        $options = [
            "debug" => $_ENV["ISDEV"]
        ];

        if (!$_ENV["ISDEV"]) {
            $options["cache"] = __DIR__ . "/../.cache";
        }

        $twig = new \Twig\Environment($loader, $options);

        if ($_ENV["ISDEV"]) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        return $twig;
    },
    "LoginController" => function (ContainerInterface $c) {
        return new LoginController($c->get("Twig"), $c->get("AuthorizeUseCase"));
    },
    "ProblemController" => function (ContainerInterface $c) {
        return new ProblemController(
            $c->get("Twig"),
            $c->get("GetProblemByIdUseCase"),
            $c->get("SubmitUseCase"),
            $c->get("CreateProblemUseCase"),
            $c->get("UpdateProblemTitleAndBodyUseCase"),
            $c->get("DeleteProblemUseCase")
        );
    },
    "ProblemListController" => function (ContainerInterface $c) {
        return new ProblemListController($c->get("Twig"));
    },
    "SubmissionController" => function (ContainerInterface $c) {
        return new SubmissionController(
            $c->get("Twig"),
            $c->get("GetSubmissionsByProblemIdUseCase"),
            $c->get("GetSubmissionsByProblemIdAndUserIdUseCase"),
            $c->get("GetSubmissionsByUserIdUseCase"),
            $c->get("GetSubmissionByIdUseCase"),
            $c->get("GetProblemByIdUseCase"),
            $c->get("DeleteSubmissionUseCase")
        );
    },
    "TestCaseController" => function (ContainerInterface $c) {
        return new TestCaseController(
            $c->get("Twig"),
            $c->get("GetProblemByIdUseCase"),
            $c->get("AddProblemLanguagesUseCase"),
            $c->get("RemoveProblemLanguagesUseCase"),
            $c->get("UpdateTestCaseUseCase"),
            $c->get("CreateTestCaseUseCase"),
            $c->get("EnableTestCaseUseCase"),
            $c->get("DisableTestCaseUseCase"),
            $c->get("UpdateCompileRuleUseCase")
        );
    },

    /** Application Layer */
    "AddProblemLanguagesUseCase" => function (ContainerInterface $c) {
        return new AddProblemLanguagesUseCase($c->get("ProblemRepository"));
    },
    "AuthorizeUseCase" => function (ContainerInterface $c) {
        return new AuthorizeUseCase($c->get("UserRepository"), $c->get("SessionRepository"));
    },
    "CreateProblemUseCase" => function (ContainerInterface $c) {
        return new CreateProblemUseCase($c->get("ProblemRepository"), $c->get("FileRepository"));
    },
    "CreateTestCaseUseCase" => function (ContainerInterface $c) {
        return new CreateTestCaseUseCase($c->get("ProblemRepository"), $c->get("FileRepository"));
    },
    "CreateUserUseCase" => function (ContainerInterface $c) {
        return new CreateUserUseCase($c->get("UserRepository"));
    },
    "DeleteProblemUseCase" => function (ContainerInterface $c) {
        return new DeleteProblemUseCase($c->get("ProblemRepository"));
    },
    "DeleteSubmissionUseCase" => function (ContainerInterface $c) {
        return new DeleteSubmissionUseCase($c->get("SubmissionRepository"));
    },
    "DisableTestCaseUseCase" => function (ContainerInterface $c) {
        return new DisableTestCaseUseCase($c->get("ProblemRepository"));
    },
    "EnableTestCaseUseCase" => function (ContainerInterface $c) {
        return new EnableTestCaseUseCase($c->get("ProblemRepository"));
    },
    "GetProblemByIdUseCase" => function (ContainerInterface $c) {
        return new GetProblemByIdUseCase($c->get("ProblemRepository"));
    },
    "GetProblemsUseCase" => function (ContainerInterface $c) {
        return new GetProblemsUseCase($c->get("ProblemRepository"));
    },
    "GetSubmissionByIdUseCase" => function (ContainerInterface $c) {
        return new GetSubmissionByIdUseCase($c->get("SubmissionRepository"));
    },
    "GetSubmissionsByProblemIdUseCase" => function (ContainerInterface $c) {
        return new GetSubmissionsByProblemIdUseCase($c->get("SubmissionRepository"), $c->get("ProblemRepository"));
    },
    "GetSubmissionsByProblemIdAndUserIdUseCase" => function (ContainerInterface $c) {
        return new GetSubmissionsByProblemIdAndUserIdUseCase($c->get("SubmissionRepository"), $c->get("ProblemRepository"), $c->get("UserRepository"));
    },
    "GetSubmissionsByUserIdUseCase" => function (ContainerInterface $c) {
        return new GetSubmissionsByUserIdUseCase($c->get("SubmissionRepository"), $c->get("UserRepository"));
    },
    "RemoveProblemLanguagesUseCase" => function (ContainerInterface $c) {
        return new RemoveProblemLanguagesUseCase($c->get("ProblemRepository"));
    },
    "SubmitUseCase" => function (ContainerInterface $c) {
        return new SubmitUseCase($c->get("UserRepository"), $c->get("ProblemRepository"), $c->get("FileRepository"), $c->get("SubmissionRepository"), $c->get("JudgeQueueRepository"));
    },
    "UpdateCompileRuleUseCase" => function (ContainerInterface $c) {
        return new UpdateCompileRuleUseCase($c->get("ProblemRepository"));
    },
    "UpdateProblemTitleAndBodyUseCase" => function (ContainerInterface $c) {
        return new UpdateProblemTitleAndBodyUseCase($c->get("ProblemRepository"));
    },
    "UpdateTestCaseUseCase" => function (ContainerInterface $c) {
        return new UpdateTestCaseUseCase($c->get("ProblemRepository"));
    },
    "ValidateSessionUseCase" => function (ContainerInterface $c) {
        return new ValidateSessionUseCase($c->get("UserRepository"), $c->get("SessionRepository"));
    }
]);

$GLOBALS["container"] = $containerBuilder->build();

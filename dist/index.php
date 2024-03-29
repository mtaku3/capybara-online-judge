<?php

declare(strict_types=1);

use App\Application\ValidateSession\ValidateSessionRequest;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Presentation\Router\DataCollection\RouteCollection;
use App\Presentation\Router\Exceptions\HttpException;
use App\Presentation\Router\HttpStatus;
use App\Presentation\Router\Response;
use App\Presentation\Router\Request;
use App\Presentation\Router\Router;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

$router = new Router();
/** @var ContainerInterface */
$container = $GLOBALS["container"];
/** @var \Twig\Environment */
$twig = $container->get("Twig");

$logger = new Logger("App");
$handler = new StreamHandler("php://stdout", Level::Debug);
$formatter = new LineFormatter();
$formatter->includeStacktraces();
$handler->setFormatter($formatter);
$logger->pushHandler($handler);

$router->onError(function (Router $router, string $msg, string $type, Exception|Throwable $err) use ($twig, $logger) {
    $logger->error($err);

    $code = 500;

    $router->response()->body($twig->render("Error.twig", [
        "code" => $code,
        "httpMessage" => HttpStatus::getMessageFromCode($code),
        "message" => $message ?? ""
    ]));
    $router->response()->code($code);
});

$router->onHttpError(function (int $code, Router $router, RouteCollection $matched, array $methods_matched, HttpException $http_exception) use ($twig, $logger) {
    $logger->error($http_exception);

    switch ($code) {
        case 401:
            $message = "権限が不足しています";
            break;
        case 404:
            $message = "ページが見つかりませんでした";
            break;
        case 500:
            $message = "問題が発生しました。サーバー管理者に問い合わせてください。";
            break;
    }

    $router->response()->body($twig->render("Error.twig", [
        "code" => $code,
        "httpMessage" => HttpStatus::getMessageFromCode($code),
        "message" => $message ?? ""
    ]));
    $router->response()->code($code);
});

$router->respond(function (Request $req, Response $res) use ($container, $twig) {
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
        } catch (Throwable) {
            $res->cookie("x-user-id", expiry: 1);
            $res->cookie("x-access-token", expiry: 1);
            $res->cookie("x-refresh-token", expiry: 1);
        }

        $twig->addGlobal("user", $req->user);
    }
});

$router->get("/", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemListController")->get($req, $res);
});

$router->get("/auth/register", function (Request $req, Response $res) use ($container) {
    $container->get("RegisterController")->get($req, $res);
});

$router->post("/auth/register", function (Request $req, Response $res) use ($container) {
    $container->get("RegisterController")->handleForm($req, $res);
});

$router->get("/auth/login", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->get($req, $res);
});

$router->post("/auth/login", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->handleForm($req, $res);
});

$router->get("/auth/logout", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->handleLogout($req, $res);
});

$router->get("/auth/changePassword", function (Request $req, Response $res) use ($container) {
    $container->get("ChangeUserPasswordController")->get($req, $res);
});

$router->post("/auth/changePassword", function (Request $req, Response $res) use ($container) {
    $container->get("ChangeUserPasswordController")->handleForm($req, $res);
});

$router->post("/problem", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->handleCreate($req, $res);
});

$router->get("/problem/[s:problemId]", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("ProblemController")->get($req, $res);
});

$router->post("/problem/[s:problemId]/update", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("ProblemController")->handleUpdate($req, $res);
});

$router->get("/problem/[s:problemId]/delete", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("ProblemController")->handleDelete($req, $res);
});

$router->post("/problem/[s:problemId]/submit", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("ProblemController")->handleSubmit($req, $res);
});

$router->get("/problem/[s:problemId]/submissions", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("SubmissionController")->getByProblem($req, $res);
});

$router->get("/problem/[s:problemId]/testcases", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("TestCaseController")->get($req, $res);
});

$router->post("/problem/[s:problemId]/testcases/update", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("TestCaseController")->handleLanguageChanges($req, $res);
});

$router->post("/problem/[s:problemId]/testcase", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $container->get("TestCaseController")->handleCreate($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/inputfile/download", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $twig->addGlobal("testCaseId", new TestCaseId($req->testCaseId));
    $container->get("TestCaseController")->handleDownloadInputFile($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/outputfile/download", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $twig->addGlobal("testCaseId", new TestCaseId($req->testCaseId));
    $container->get("TestCaseController")->handleDownloadOutputFile($req, $res);
});

$router->post("/problem/[s:problemId]/testcase/[s:testCaseId]/update", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $twig->addGlobal("testCaseId", new TestCaseId($req->testCaseId));
    $container->get("TestCaseController")->handleUpdate($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/enable", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $twig->addGlobal("testCaseId", new TestCaseId($req->testCaseId));
    $container->get("TestCaseController")->handleEnable($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/disable", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("problemId", new ProblemId($req->problemId));
    $twig->addGlobal("testCaseId", new TestCaseId($req->testCaseId));
    $container->get("TestCaseController")->handleDisable($req, $res);
});

$router->get("/user/[s:userId]/submissions", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->getByUser($req, $res);
});

$router->get("/submission/[s:submissionId]", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("submissionId", new SubmissionId($req->submissionId));
    $container->get("SubmissionController")->get($req, $res);
});

$router->get("/submission/[s:submissionId]/download", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("submissionId", new SubmissionId($req->submissionId));
    $container->get("SubmissionController")->handleDownload($req, $res);
});

$router->get("/submission/[s:submissionId]/delete", function (Request $req, Response $res) use ($container, $twig) {
    $twig->addGlobal("submissionId", new SubmissionId($req->submissionId));
    $container->get("SubmissionController")->handleDelete($req, $res);
});

$router->dispatch();

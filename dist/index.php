<?php

declare(strict_types=1);

use App\Application\ValidateSession\ValidateSessionRequest;
use App\Domain\User\ValueObject\UserId;
use App\Presentation\Router\HttpStatus;
use App\Presentation\Router\Response;
use App\Presentation\Router\Request;
use App\Presentation\Router\Router;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

$router = new Router();
/** @var ContainerInterface */
$container = $GLOBALS["container"];
/** @var \Twig\Environment */
$twig = $container->get("Twig");

$router->onHttpError(function (int $code, Router $router) use ($twig) {
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

$router->get("/auth/login", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->get($req, $res);
});

$router->post("/auth/login", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->handleForm($req, $res);
});

$router->get("/auth/logout", function (Request $req, Response $res) use ($container) {
    $container->get("LoginController")->handleLogout($req, $res);
});

$router->post("/problem", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->handleCreate($req, $res);
});

$router->get("/problem/[s:problemId]", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->get($req, $res);
});

$router->post("/problem/[s:problemId]/update", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->handleUpdate($req, $res);
});

$router->post("/problem/[s:problemId]/delete", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->handleDelete($req, $res);
});

$router->post("/problem/[s:problemId]/submit", function (Request $req, Response $res) use ($container) {
    $container->get("ProblemController")->handleSubmit($req, $res);
});

$router->get("/problem/[s:problemId]/submissions", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->getByProblem($req, $res);
});

$router->get("/problem/[s:problemId]/testcases", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->get($req, $res);
});

$router->post("/problem/[s:problemId]/testcases/update", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleLanguageChanges($req, $res);
});

$router->post("/problem/[s:problemId]/testcase", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleCreate($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/inputfile/download", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleDownloadInputFile($req, $res);
});

$router->get("/problem/[s:problemId]/testcase/[s:testCaseId]/outputfile/download", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleDownloadOutputFile($req, $res);
});

$router->post("/problem/[s:problemId]/testcase/[s:testCaseId]/update", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleUpdate($req, $res);
});

$router->post("/problem/[s:problemId]/testcase/[s:testCaseId]/enable", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleEnable($req, $res);
});

$router->post("/problem/[s:problemId]/testcase/[s:testCaseId]/disable", function (Request $req, Response $res) use ($container) {
    $container->get("TestCaseController")->handleDisable($req, $res);
});

$router->get("/user/[s:userId]/submissions", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->getByUser($req, $res);
});

$router->get("/submission/[s:submissionId]", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->get($req, $res);
});

$router->get("/submission/[s:submissionId]/download", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->handleDownload($req, $res);
});

$router->post("/submission/[s:submissionId]/delete", function (Request $req, Response $res) use ($container) {
    $container->get("SubmissionController")->handleDelete($req, $res);
});

$router->dispatch();

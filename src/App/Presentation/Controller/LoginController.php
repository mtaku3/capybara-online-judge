<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Authorize\AuthorizeRequest;
use App\Application\Authorize\AuthorizeUseCase;
use App\Application\Exception\WrongPasswordException;
use App\Application\PurgeSessions\PurgeSessionsRequest;
use App\Application\PurgeSessions\PurgeSessionsUseCase;
use App\Infrastructure\Repository\User\Exception\UserNotFoundException;
use App\Presentation\Router\Response;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use DomainException;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class LoginController
{
    /**
     * @var Environment
     */
    private readonly Environment $Twig;
    /**
     * @var AuthorizeUseCase
     */
    private readonly AuthorizeUseCase $AuthorizeUseCase;
    /**
     * @var PurgeSessionsUseCase
     */
    private readonly PurgeSessionsUseCase $PurgeSessionsUseCase;

    /**
     * @param Environment $twig
     * @return void
     */
    public function __construct(Environment $twig, AuthorizeUseCase $authorizeUseCase, PurgeSessionsUseCase $purgeSessionsUseCase)
    {
        $this->Twig = $twig;
        $this->AuthorizeUseCase = $authorizeUseCase;
        $this->PurgeSessionsUseCase = $purgeSessionsUseCase;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     */
    public function get(Request $req, Response $res): void
    {
        $res->body($this->Twig->render("auth/login.twig"));
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     * @throws Exception
     * @throws DomainException
     */
    public function handleForm(Request $req, Response $res): void
    {
        try {
            $authorizeResponse = $this->AuthorizeUseCase->handle(new AuthorizeRequest($req->username, $req->password));

            $res->cookie("x-user-id", (string)$authorizeResponse->Session->getUserId(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-access-token", (string)$authorizeResponse->AccessToken, $authorizeResponse->AccessToken->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-refresh-token", (string)$authorizeResponse->Session->getRefreshToken(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");

            $res->redirect("/");
        } catch (WrongPasswordException|UserNotFoundException) {
            $res->body($this->Twig->render("auth/login.twig", [
                "error" => "ユーザー名、またはパスワードが間違っています"
            ]));
        }

        try {
            $this->PurgeSessionsUseCase->handle(new PurgeSessionsRequest($req->user));
        } catch (Throwable) {
            // ignore
        }
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws LockedResponseException
     */
    public function handleLogout(Request $req, Response $res): void
    {
        $res->cookie("x-user-id", expiry: 1);
        $res->cookie("x-access-token", expiry: 1);
        $res->cookie("x-refresh-token", expiry: 1);

        $res->redirect("/");

        try {
            $this->PurgeSessionsUseCase->handle(new PurgeSessionsRequest($req->user));
        } catch (Throwable) {
            // ignore
        }
    }
}

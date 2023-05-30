<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Authorize\AuthorizeRequest;
use App\Application\Authorize\AuthorizeUseCase;
use App\Application\Exception\WrongPasswordException;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use DomainException;
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
     * @param Environment $twig
     * @return void
     */
    public function __construct(Environment $twig, AuthorizeUseCase $authorizeUseCase)
    {
        $this->Twig = $twig;
        $this->AuthorizeUseCase = $authorizeUseCase;
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     */
    public function get(Request $req, AbstractResponse $res): void
    {
        $res->body($this->Twig->render("auth/login.twig"))->send();
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws WrongPasswordException
     * @throws Exception
     * @throws DomainException
     */
    public function handleForm(Request $req, AbstractResponse $res): void
    {
        try {
            $authorizeResponse = $this->AuthorizeUseCase->handle(new AuthorizeRequest($req->username, $req->password));

            $res->cookie("x-user-id", (string)$authorizeResponse->Session->getUserId(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-access-token", (string)$authorizeResponse->AccessToken, $authorizeResponse->AccessToken->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-refresh-token", (string)$authorizeResponse->Session->getRefreshToken(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");

            $res->redirect("/");
        } catch (Exception $e) {
            $res->body($this->Twig->render("auth/login.twig", [
                "error" => "ユーザー名、またはパスワードが間違っています"
            ]))->send();
        }
    }

    public function handleLogout(Request $req, AbstractResponse $res): void
    {
        try {
            $res->cookie("x-user-id", expiry: 1);
            $res->cookie("x-access-token", expiry: 1);
            $res->cookie("x-refresh-token", expiry: 1);

            $res->redirect("/");
        } catch (Exception $e) {
            $res->code(500)->send();
        }
    }
}

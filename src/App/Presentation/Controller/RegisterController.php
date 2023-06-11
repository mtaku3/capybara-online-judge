<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Authorize\AuthorizeRequest;
use App\Application\Authorize\AuthorizeUseCase;
use App\Application\CreateUser\CreateUserRequest;
use App\Application\CreateUser\CreateUserUseCase;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Request;
use Exception;
use Throwable;
use Twig\Environment;

class RegisterController
{
    /**
     * @var Environment
     */
    private readonly Environment $Twig;
    /**
     * @var CreateUserUseCase
     */
    private readonly CreateUserUseCase $CreateUserUseCase;
    /**
     * @var AuthorizeUseCase
     */
    private readonly AuthorizeUseCase $AuthorizeUseCase;

    /**
     * @param Environment $twig
     * @param CreateUserUseCase $createUserUseCase
     * @param AuthorizeUseCase $authorizeUseCase
     * @return void
     */
    public function __construct(Environment $twig, CreateUserUseCase $createUserUseCase, AuthorizeUseCase $authorizeUseCase)
    {
        $this->Twig = $twig;
        $this->CreateUserUseCase = $createUserUseCase;
        $this->AuthorizeUseCase = $authorizeUseCase;
    }

    public function get(Request $req, AbstractResponse $res)
    {
        $res->body($this->Twig->render("Auth/Register.twig"))->send();
    }

    public function handleForm(Request $req, AbstractResponse $res)
    {
        try {
            $createUserResponse = $this->CreateUserUseCase->handle(
                new CreateUserRequest($req->username, $req->password, false)
            );
        } catch (Throwable $e) {
            $res->body($this->Twig->render("Auth/Register.twig", [
                "error" => "ユーザー名が既に使用されているか、パスワードが無効です"
            ]))->send();
            return;
        }
        try {
            $authorizeResponse = $this->AuthorizeUseCase->handle(
                new AuthorizeRequest($createUserResponse->User->getUsername(), $req->password)
            );

            $res->cookie("x-user-id", (string)$authorizeResponse->Session->getUserId(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-access-token", (string)$authorizeResponse->AccessToken, $authorizeResponse->AccessToken->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");
            $res->cookie("x-refresh-token", (string)$authorizeResponse->Session->getRefreshToken(), $authorizeResponse->Session->getExpiresAt()->getTimestamp(), secure: $_ENV["ISDEV"] ? false : true, httponly: true, samesite: "strict");

            $res->redirect("/");
        } catch (Throwable $e) {
            $res->code(500)->send();
        }
    }
}

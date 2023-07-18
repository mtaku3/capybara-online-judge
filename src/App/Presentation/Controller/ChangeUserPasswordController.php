<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\ChangeUserPassword\ChangeUserPasswordRequest;
use App\Application\ChangeUserPassword\ChangeUserPasswordUseCase;
use App\Application\Exception\WrongPasswordException;
use App\Domain\User\Exception\InvalidPasswordException;
use App\Infrastructure\Repository\User\Exception\UserNotFoundException;
use App\Presentation\Router\Response;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use DomainException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class ChangeUserPasswordController
{
    /**
     * @var Environment
     */
    private readonly Environment $Twig;

    /**
     * @var ChangeUserPasswordUseCase
     */
    private readonly ChangeUserPasswordUseCase $ChangeUserPasswordUseCase;

    /**
     * @param Environment $twig
     * @return void
     */
    public function __construct(Environment $twig, ChangeUserPasswordUseCase $changeUserPasswordUseCase)
    {
        $this->Twig = $twig;
        $this->ChangeUserPasswordUseCase = $changeUserPasswordUseCase;
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
        $res->body($this->Twig->render("Auth/ChangePassword.twig"));
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
            $this->ChangeUserPasswordUseCase->handle(new ChangeUserPasswordRequest(
                $req->username,
                $req->currentPassword,
                $req->newPassword
            ));

            $res->redirect("/auth/login");
        } catch (WrongPasswordException|UserNotFoundException) {
            $res->body($this->Twig->render("Auth/ChangePassword.twig", [
                "error" => "ユーザー名、またはパスワードが間違っています"
            ]));
        } catch (InvalidPasswordException) {
            $res->body($this->Twig->render("Auth/ChangePassword.twig", [
                "error" => "使用できないパスワードです"
            ]));
        }
    }
}

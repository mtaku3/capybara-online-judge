<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\GetProblems\GetProblemsRequest;
use App\Application\GetProblems\GetProblemsUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Presentation\Router\Response;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class ProblemListController
{
    public const LimitPerPage = 10;

    /**
     * @var Environment
     */
    private readonly Environment $Twig;

    /**
     * @var GetProblemsUseCase
     */
    private readonly GetProblemsUseCase $GetProblemsUseCase;

    /**
     * @param Environment $twig
     * @param GetProblemsUseCase $getProblemsUseCase
     * @return void
     */
    public function __construct(Environment $twig, GetProblemsUseCase $getProblemsUseCase)
    {
        $this->Twig = $twig;
        $this->GetProblemsUseCase = $getProblemsUseCase;
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
    public function get(Request $req, Response $res)
    {
        $getProblemsResponse = $this->GetProblemsUseCase->handle(new GetProblemsRequest(intval($req->param("page", 1)), self::LimitPerPage));
        $res->body($this->Twig->render("ProblemList.twig", [
            "user" => $req->user,
            "problems" => $getProblemsResponse->Problems,
            "page" => intval($req->param("page", 1)),
            "totalNumberOfProblems" => $getProblemsResponse->Count,
            "totalNumberOfPages" => intval(ceil($getProblemsResponse->Count / self::LimitPerPage)),
            "limitPerPage" => self::LimitPerPage,
            "availableLanguages" => Language::cases()
        ]));
    }
}

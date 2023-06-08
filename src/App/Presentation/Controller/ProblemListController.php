<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

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
     * @param Environment $twig
     * @return void
     */
    public function __construct(Environment $twig)
    {
        $this->Twig = $twig;
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
        $res->body($this->Twig->render("ProblemList.twig"));
    }
}

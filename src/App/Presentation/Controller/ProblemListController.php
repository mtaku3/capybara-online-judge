<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class ProblemListController
{
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
     * @param AbstractResponse $res
     * @return void
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     */
    public function get(Request $req, AbstractResponse $res)
    {
        $res->body($this->Twig->render("ProblemList.twig"))->send();
    }
}

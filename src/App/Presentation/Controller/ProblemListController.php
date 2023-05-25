<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Request;
use DateTimeImmutable;
use Twig\Environment;

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

    public function get(Request $req, AbstractResponse $res)
    {
        $res->body($this->Twig->render("ProblemList.twig", [
            "user" => $req->user,
            "problems" => [
                new Problem(ProblemId::NextIdentity(), "This is a problem 1", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
                new Problem(ProblemId::NextIdentity(), "This is a problem 2", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
                new Problem(ProblemId::NextIdentity(), "This is a problem 3", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
                new Problem(ProblemId::NextIdentity(), "This is a problem 4", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
                new Problem(ProblemId::NextIdentity(), "This is a problem 5", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
                new Problem(ProblemId::NextIdentity(), "This is a problem 6", "Test string for body", 1000, 1000, array(), array(), new DateTimeImmutable()),
            ],
            "page" => intval($req->page),
            "totalNumberOfProblems" => 20
        ]))->send();
    }
}

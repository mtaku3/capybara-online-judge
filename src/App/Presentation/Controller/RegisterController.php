<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Request;
use Twig\Environment;

class RegisterController
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

    public function get(Request $req, AbstractResponse $res)
    {
        $res->body($this->Twig->render("auth/register.twig"))->send();
    }
}

<?php

declare(strict_types=1);

namespace App\Application\CreateProblem;

use App\Domain\Problem\Entity\Problem;

class CreateProblemResponse
{
    /**
     * @var Problem
     */
    public readonly Problem $Problem;

    /**
     * @param Problem $problem
     * @return void
     */
    public function __construct(Problem $problem)
    {
        $this->Problem = $problem;
    }
}

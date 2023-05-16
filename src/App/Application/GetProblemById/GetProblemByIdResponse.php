<?php

declare(strict_types=1);

namespace App\Application\GetProblemById;

use App\Domain\Problem\Entity\Problem;

class GetProblemByIdResponse
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

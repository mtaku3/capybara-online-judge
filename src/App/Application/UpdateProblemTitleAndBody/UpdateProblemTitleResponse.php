<?php

declare(strict_types=1);

namespace App\Application\UpdateProblemTitleAndBody;

use App\Domain\Problem\Entity\Problem;

class UpdateProblemTitleResponse
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

<?php

declare(strict_types=1);

namespace App\Application\DisableTestCase;

use App\Domain\Problem\Entity\Problem;

class DisableTestCaseResponse
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

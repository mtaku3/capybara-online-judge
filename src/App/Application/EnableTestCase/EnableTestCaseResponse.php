<?php

declare(strict_types=1);

namespace App\Application\EnableTestCase;

use App\Domain\Problem\Entity\Problem;

class EnableTestCaseResponse
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

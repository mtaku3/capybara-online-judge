<?php

declare(strict_types=1);

namespace App\Application\GetProblems;

class GetProblemsResponse
{
    /**
     * @var \App\Domain\Problem\Entity\Problem[]
     */
    public readonly array $Problems;

    /**
     * @param \App\Domain\Problem\Entity\Problem[] $problems
     * @return void
     */
    public function __construct(array $problems)
    {
        $this->Problems = $problems;
    }
}

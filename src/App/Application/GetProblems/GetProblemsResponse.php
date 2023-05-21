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
     * @var int
     */
    public readonly int $Count;

    /**
     * @param array $problems 
     * @param int $count 
     * @return void 
     */
    public function __construct(array $problems, int $count)
    {
        $this->Problems = $problems;
        $this->Count = $count;
    }
}

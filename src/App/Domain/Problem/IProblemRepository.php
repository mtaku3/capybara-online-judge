<?php

declare(strict_types=1);

namespace App\Domain\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\ValueObject\ProblemId;

interface IProblemRepository
{
    /** @return array<Problem>  */
    public function getAll(): array;

    /**
     * @param ProblemId $problemId
     * @return Problem
     */
    public function findByProblemId(ProblemId $problemId): Problem;

    /**
     * @param Problem $problem
     * @return void
     */
    public function save(Problem $problem): void;
}

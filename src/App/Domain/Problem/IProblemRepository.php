<?php

declare(strict_types=1);

namespace App\Domain\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\ValueObject\ProblemId;

interface IProblemRepository
{
    /** @return Problem[]  */
    public function getAll(): array;

    /**
     * @param ProblemId $id
     * @return Problem
     */
    public function findById(ProblemId $id): Problem;

    /**
     * @param Problem $problem
     * @return void
     */
    public function save(Problem $problem): void;
}

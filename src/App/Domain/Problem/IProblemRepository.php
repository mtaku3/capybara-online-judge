<?php

declare(strict_types=1);

namespace App\Domain\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\ValueObject\ProblemId;

interface IProblemRepository
{
    /**
     * @param int $page
     * @param int $limit
     * @return Problem[]
     */
    public function fetchAll(int $page = 1, int $limit = 0): array;

    /** @return int  */
    public function count(): int;

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

    /**
     * @param Problem $problem
     * @return void
     */
    public function delete(Problem $problem): void;
}

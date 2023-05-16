<?php

declare(strict_types=1);

namespace App\Application\DeleteProblem;

use App\Domain\Problem\ValueObject\ProblemId;

class DeleteProblemRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;

    /**
     * @param ProblemId $problemId
     * @return void
     */
    public function __construct(ProblemId $problemId)
    {
        $this->ProblemId = $problemId;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemId;

use App\Domain\Problem\ValueObject\ProblemId;

class GetSubmissionsByProblemIdRequest
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

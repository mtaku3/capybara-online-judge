<?php

declare(strict_types=1);

namespace App\Application\GetProblemById;

use App\Domain\Problem\ValueObject\ProblemId;

class GetProblemByIdRequest
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

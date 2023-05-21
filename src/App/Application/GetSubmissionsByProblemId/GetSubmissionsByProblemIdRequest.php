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
     * @var int
     */
    public readonly int $Page;
    /**
     * @var int
     */
    public readonly int $Limit;

    /**
     * @param ProblemId $problemId
     * @param int $page
     * @param int $limit
     * @return void
     */
    public function __construct(ProblemId $problemId, int $page, int $limit)
    {
        $this->ProblemId = $problemId;
        $this->Page = $page;
        $this->Limit = $limit;
    }
}

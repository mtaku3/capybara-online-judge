<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemIdAndUserId;

use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\User\ValueObject\UserId;

class GetSubmissionsByProblemIdAndUserIdRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var UserId
     */
    public readonly UserId $UserId;
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
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return void
     */
    public function __construct(ProblemId $problemId, UserId $userId, int $page, int $limit)
    {
        $this->ProblemId = $problemId;
        $this->UserId = $userId;
        $this->Page = $page;
        $this->Limit = $limit;
    }
}

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
     * @param ProblemId $problemId
     * @param UserId $userId
     * @return void
     */
    public function __construct(ProblemId $problemId, UserId $userId)
    {
        $this->ProblemId = $problemId;
        $this->UserId = $userId;
    }
}

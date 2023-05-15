<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\User\ValueObject\UserId;

class SubmitRequest
{
    /**
     * @var UserId
     */
    public readonly UserId $UserId;
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var Language
     */
    public readonly Language $Language;
    /**
     * @var int
     */
    public readonly int $CodeLength;

    /**
     * @param UserId $userId
     * @param ProblemId $problemId
     * @param Language $language
     * @param int $codeLength
     * @return void
     */
    public function __construct(UserId $userId, ProblemId $problemId, Language $language, int $codeLength)
    {
        $this->UserId = $userId;
        $this->ProblemId = $problemId;
        $this->Language = $language;
        $this->CodeLength = $codeLength;
    }
}

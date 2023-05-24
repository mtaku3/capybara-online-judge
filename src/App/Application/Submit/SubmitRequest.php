<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionType;
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
     * @var SubmissionType
     */
    public readonly SubmissionType $SubmissionType;
    /**
     * @var string
     */
    public readonly string $UploadedFilePath;

    /**
     * @param UserId $userId
     * @param ProblemId $problemId
     * @param Language $language
     * @param SubmissionType $submissionType
     * @param string $uploadedFilePath
     * @return void
     */
    public function __construct(UserId $userId, ProblemId $problemId, Language $language, SubmissionType $submissionType, string $uploadedFilePath)
    {
        $this->UserId = $userId;
        $this->ProblemId = $problemId;
        $this->Language = $language;
        $this->SubmissionType = $submissionType;
        $this->UploadedFilePath = $uploadedFilePath;
    }
}

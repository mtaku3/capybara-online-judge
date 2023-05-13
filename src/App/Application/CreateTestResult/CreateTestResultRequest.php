<?php

declare(strict_types=1);

namespace App\Application\CreateTestResult;

use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Submission\ValueObject\TestResultJudgeResult;

class CreateTestResultRequest
{
    public readonly SubmissionId $SubmissionId;
    public readonly TestCaseId $TestCaseId;
    public readonly TestResultJudgeResult $JudgeResult;
    public readonly int $ExecutionTime;
    public readonly int $ConsumedMemory;

    /**
     * @param SubmissionId $submissionId
     * @param TestCaseId $testCaseId
     * @param TestResultJudgeResult $judgeResult
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     */
    public function __construct(SubmissionId $submissionId, TestCaseId $testCaseId, TestResultJudgeResult $judgeResult, int $executionTime, int $consumedMemory)
    {
        $this->SubmissionId = $submissionId;
        $this->TestCaseId = $testCaseId;
        $this->JudgeResult = $judgeResult;
        $this->ExecutionTime = $executionTime;
        $this->ConsumedMemory = $consumedMemory;
    }
}

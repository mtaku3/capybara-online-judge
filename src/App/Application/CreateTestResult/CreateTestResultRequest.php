<?php

declare(strict_types=1);

namespace App\Application\CreateTestResult;

use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Submission\ValueObject\TestResultJudgeResult;

class CreateTestResultRequest
{
    /**
     * @var SubmissionId
     */
    public readonly SubmissionId $SubmissionId;
    /**
     * @var TestCaseId
     */
    public readonly TestCaseId $TestCaseId;
    /**
     * @var TestResultJudgeResult
     */
    public readonly TestResultJudgeResult $JudgeResult;
    /**
     * @var int
     */
    public readonly int $ExecutionTime;
    /**
     * @var int
     */
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

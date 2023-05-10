<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Common\ValueObject\JudgeResult;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\TestResultId;

class TestResult
{
    private TestResultId $Id;
    private SubmissionId $SubmissionId;
    private TestCaseId $TestCaseId;
    private JudgeResult $JudgeResult;
    private int $ExecutionTime;
    private int $ConsumedMemory;

    /**
     * @param TestResultId $id
     * @param SubmissionId $submissionId
     * @param TestCaseId $testCaseId
     * @param JudgeResult $judgeResult
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     */
    public function __construct(TestResultId $id, SubmissionId $submissionId, TestCaseId $testCaseId, JudgeResult $judgeResult, int $executionTime, int $consumedMemory)
    {
        $this->Id = $id;
        $this->SubmissionId = $submissionId;
        $this->TestCaseId = $testCaseId;
        $this->JudgeResult = $judgeResult;
        $this->ExecutionTime = $executionTime;
        $this->ConsumedMemory = $consumedMemory;
    }

    /** @return TestResultId  */
    public function getId(): TestResultId
    {
        return $this->Id;
    }

    /** @return SubmissionId  */
    public function getSubmissionId(): SubmissionId
    {
        return $this->SubmissionId;
    }

    /** @return TestCaseId  */
    public function getTestCaseId(): TestCaseId
    {
        return $this->TestCaseId;
    }

    /** @return JudgeResult  */
    public function getJudgeResult(): JudgeResult
    {
        return $this->JudgeResult;
    }

    /** @return int  */
    public function getExecutionTime(): int
    {
        return $this->ExecutionTime;
    }

    /** @return int  */
    public function getConsumedMemory(): int
    {
        return $this->ConsumedMemory;
    }
}

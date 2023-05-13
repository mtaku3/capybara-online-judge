<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\TestResultId;
use App\Domain\Submission\ValueObject\TestResultJudgeResult;

class TestResult
{
    /**
     * @var TestResultId
     */
    private TestResultId $Id;
    /**
     * @var SubmissionId
     */
    private SubmissionId $SubmissionId;
    /**
     * @var TestCaseId
     */
    private TestCaseId $TestCaseId;
    /**
     * @var TestResultJudgeResult
     */
    private TestResultJudgeResult $JudgeResult;
    /**
     * @var int
     */
    private int $ExecutionTime;
    /**
     * @var int
     */
    private int $ConsumedMemory;

    /**
     * @param TestResultId $id
     * @param SubmissionId $submissionId
     * @param TestCaseId $testCaseId
     * @param TestResultJudgeResult $judgeResult
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     */
    public function __construct(TestResultId $id, SubmissionId $submissionId, TestCaseId $testCaseId, TestResultJudgeResult $judgeResult, int $executionTime, int $consumedMemory)
    {
        $this->Id = $id;
        $this->SubmissionId = $submissionId;
        $this->TestCaseId = $testCaseId;
        $this->JudgeResult = $judgeResult;
        $this->ExecutionTime = $executionTime;
        $this->ConsumedMemory = $consumedMemory;
    }

    /**
     * @param SubmissionId $submissionId
     * @param TestCaseId $testCaseId
     * @param TestResultJudgeResult $judgeResult
     * @param int $executionTime
     * @param int $consumedMemory
     * @return TestResult
     */
    public static function _create(SubmissionId $submissionId, TestCaseId $testCaseId, TestResultJudgeResult $judgeResult, int $executionTime, int $consumedMemory): TestResult
    {
        return new TestResult(TestResultId::NextIdentity(), $submissionId, $testCaseId, $judgeResult, $executionTime, $consumedMemory);
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

    /** @return TestResultJudgeResult  */
    public function getJudgeResult(): TestResultJudgeResult
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

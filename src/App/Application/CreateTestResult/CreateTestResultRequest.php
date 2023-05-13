<?php

declare(strict_types=1);

namespace App\Application\CreateTestResult;

use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\SubmissionId;

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
     * @var bool
     */
    public readonly bool $HasRuntimeErrorOccurred;
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
     * @param bool $hasRuntimeErrorOccurred
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     */
    public function __construct(SubmissionId $submissionId, TestCaseId $testCaseId, bool $hasRuntimeErrorOccurred, int $executionTime, int $consumedMemory)
    {
        $this->SubmissionId = $submissionId;
        $this->TestCaseId = $testCaseId;
        $this->HasRuntimeErrorOccurred = $hasRuntimeErrorOccurred;
        $this->ExecutionTime = $executionTime;
        $this->ConsumedMemory = $consumedMemory;
    }
}

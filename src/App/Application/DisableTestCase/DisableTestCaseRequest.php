<?php

declare(strict_types=1);

namespace App\Application\DisableTestCase;

use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;

class DisableTestCaseRequest
{
    public readonly ProblemId $ProblemId;
    public readonly TestCaseId $TestCaseId;

    /**
     * @param ProblemId $problemId
     * @param TestCaseId $testCaseId
     * @return void
     */
    public function __construct(ProblemId $problemId, TestCaseId $testCaseId)
    {
        $this->ProblemId = $problemId;
        $this->TestCaseId = $testCaseId;
    }
}

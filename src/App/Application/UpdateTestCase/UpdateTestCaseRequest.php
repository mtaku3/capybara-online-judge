<?php

declare(strict_types=1);

namespace App\Application\UpdateTestCase;

use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;

class UpdateTestCaseRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var TestCaseId
     */
    public readonly TestCaseId $TestCaseId;
    /**
     * @var ExecutionRuleDTO[]
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param ProblemId $problemId
     * @param TestCaseId $testCaseId
     * @param array $executionRuleDTOs
     * @return void
     */
    public function __construct(ProblemId $problemId, TestCaseId $testCaseId, array $executionRuleDTOs)
    {
        $this->ProblemId = $problemId;
        $this->TestCaseId = $testCaseId;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}

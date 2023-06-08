<?php

declare(strict_types=1);

namespace App\Application\CreateTestCase;

use App\Domain\Problem\ValueObject\ProblemId;

class CreateTestCaseRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var string
     */
    public readonly string $Title;
    /**
     * @var ExecutionRuleDTO[]
     */
    public readonly array $ExecutionRuleDTOs;
    /**
     * @var string
     */
    public readonly string $UploadedInputFilePath;
    /**
     * @var string
     */
    public readonly string $UploadedOutputFilePath;

    /**
     * @param ProblemId $problemId
     * @param string $title
     * @param ExecutionRuleDTO[] $executionRuleDTOs
     * @param string $uploadedInputFilePath
     * @param string $uploadedOutputFilePath
     * @return void
     */
    public function __construct(ProblemId $problemId, string $title, array $executionRuleDTOs, string $uploadedInputFilePath, string $uploadedOutputFilePath)
    {
        $this->ProblemId = $problemId;
        $this->Title = $title;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
        $this->UploadedInputFilePath = $uploadedInputFilePath;
        $this->UploadedOutputFilePath = $uploadedOutputFilePath;
    }
}

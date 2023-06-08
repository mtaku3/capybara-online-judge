<?php

declare(strict_types=1);

namespace App\Application\CreateProblem\DTO;

class TestCaseDTO
{
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
     * @param string $title
     * @param array $executionRuleDTOs
     * @param string $uploadedInputFilePath
     * @param string $uploadedOutputFilePath
     * @return void
     */
    public function __construct(string $title, array $executionRuleDTOs, string $uploadedInputFilePath, string $uploadedOutputFilePath)
    {
        $this->Title = $title;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
        $this->UploadedInputFilePath = $uploadedInputFilePath;
        $this->UploadedOutputFilePath = $uploadedOutputFilePath;
    }
}

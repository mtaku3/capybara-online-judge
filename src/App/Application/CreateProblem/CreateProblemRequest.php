<?php

declare(strict_types=1);

namespace App\Application\CreateProblem;

class CreateProblemRequest
{
    public readonly string $Title;
    public readonly string $Body;
    public readonly int $TimeConstraint;
    public readonly int $MemoryConstraint;
    /**
     * @var array<CompileRuleFactoryDTO>
     */
    public readonly array $CompileRuleDTOs;
    /**
     * @var array<TestCaseFactoryDTO>
     */
    public readonly array $TestCaseDTOs;

    /**
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param array<CompileRuleFactoryDTO> $compileRuleDTOs
     * @param array<TestCaseFactoryDTO> $testCaseDTOs
     * @return void
     */
    public function __construct(string $title, string $body, int $timeConstraint, int $memoryConstraint, array $compileRuleDTOs, array $testCaseDTOs)
    {
        $this->Title = $title;
        $this->Body = $body;
        $this->TimeConstraint = $timeConstraint;
        $this->MemoryConstraint = $memoryConstraint;
        $this->CompileRuleDTOs = $compileRuleDTOs;
        $this->TestCaseDTOs = $testCaseDTOs;
    }
}

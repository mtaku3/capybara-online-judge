<?php

declare(strict_types=1);

namespace App\Application\CreateProblem;

use App\Application\CreateProblem\DTO\CompileRuleDTO;
use App\Application\CreateProblem\DTO\TestCaseDTO;

class CreateProblemRequest
{
    /**
     * @var string
     */
    public readonly string $Title;
    /**
     * @var string
     */
    public readonly string $Body;
    /**
     * @var int
     */
    public readonly int $TimeConstraint;
    /**
     * @var int
     */
    public readonly int $MemoryConstraint;
    /**
     * @var CompileRuleDTO[]
     */
    public readonly array $CompileRuleDTOs;
    /**
     * @var TestCaseDTO[]
     */
    public readonly array $TestCaseDTOs;

    /**
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param CompileRuleDTO[] $compileRuleDTOs
     * @param TestCaseDTO[] $testCaseDTOs
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

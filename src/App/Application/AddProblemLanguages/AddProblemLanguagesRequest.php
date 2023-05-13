<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages;

use App\Domain\Problem\ValueObject\ProblemId;

class AddProblemLanguagesRequest
{
    public readonly ProblemId $ProblemId;
    /**
     * @var array<CompileRuleFactoryDTO>
     */
    public readonly array $CompileRuleDTOs;
    /**
     * @var array<ExecutionRuleFactoryDTO>
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param ProblemId $problemId
     * @param array<CompileRuleFactoryDTO> $compileRuleDTOs
     * @param array<ExecutionRuleFactoryDTO> $executionRuleDTOs
     * @return void
     */
    public function __construct(ProblemId $problemId, array $compileRuleDTOs, array $executionRuleDTOs)
    {
        $this->ProblemId = $problemId;
        $this->CompileRuleDTOs = $compileRuleDTOs;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}

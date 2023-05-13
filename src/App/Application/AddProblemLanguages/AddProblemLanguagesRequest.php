<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages;

use App\Domain\Problem\ValueObject\ProblemId;

class AddProblemLanguagesRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var CompileRuleFactoryDTO[]
     */
    public readonly array $CompileRuleDTOs;
    /**
     * @var ExecutionRuleFactoryDTO[]
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param ProblemId $problemId
     * @param CompileRuleFactoryDTO[] $compileRuleDTOs
     * @param ExecutionRuleFactoryDTO[] $executionRuleDTOs
     * @return void
     */
    public function __construct(ProblemId $problemId, array $compileRuleDTOs, array $executionRuleDTOs)
    {
        $this->ProblemId = $problemId;
        $this->CompileRuleDTOs = $compileRuleDTOs;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}

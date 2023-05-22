<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages;

use App\Application\AddProblemLanguages\DTO\CompileRuleDTO;
use App\Application\AddProblemLanguages\DTO\ExecutionRuleDTO;
use App\Domain\Problem\ValueObject\ProblemId;

class AddProblemLanguagesRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var CompileRuleDTO[]
     */
    public readonly array $CompileRuleDTOs;
    /**
     * @var ExecutionRuleDTO[]
     */
    public readonly array $ExecutionRuleDTOs;

    /**
     * @param ProblemId $problemId
     * @param CompileRuleDTO[] $compileRuleDTOs
     * @param ExecutionRuleDTO[] $executionRuleDTOs
     * @return void
     */
    public function __construct(ProblemId $problemId, array $compileRuleDTOs, array $executionRuleDTOs)
    {
        $this->ProblemId = $problemId;
        $this->CompileRuleDTOs = $compileRuleDTOs;
        $this->ExecutionRuleDTOs = $executionRuleDTOs;
    }
}

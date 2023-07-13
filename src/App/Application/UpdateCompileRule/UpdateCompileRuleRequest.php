<?php

declare(strict_types=1);

namespace App\Application\UpdateCompileRule;

use App\Domain\Problem\ValueObject\CompileRuleId;
use App\Domain\Problem\ValueObject\ProblemId;

class UpdateCompileRuleRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var CompileRuleId
     */
    public readonly CompileRuleId $CompileRuleId;
    /**
     * @var string
     */
    public readonly string $SourceCodeCompileCommand;
    /**
     * @var string
     */
    public readonly string $FileCompileCommand;

    /**
     * @param ProblemId $problemId
     * @param CompileRuleId $compileRuleId
     * @param string $sourceCodeCompileCommand
     * @param string $fileCompileCommand
     * @return void
     */
    public function __construct(ProblemId $problemId, CompileRuleId $compileRuleId, string $sourceCodeCompileCommand, string $fileCompileCommand)
    {
        $this->ProblemId = $problemId;
        $this->CompileRuleId = $compileRuleId;
        $this->SourceCodeCompileCommand = $sourceCodeCompileCommand;
        $this->FileCompileCommand = $fileCompileCommand;
    }
}

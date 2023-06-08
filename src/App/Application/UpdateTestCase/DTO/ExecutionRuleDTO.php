<?php

declare(strict_types=1);

namespace App\Application\UpdateTestCase\DTO;

use App\Domain\Problem\ValueObject\ExecutionRuleId;

class ExecutionRuleDTO
{
    /**
     * @var ExecutionRuleId
     */
    public readonly ExecutionRuleId $ExecutionRuleId;
    /**
     * @var string
     */
    public readonly string $SourceCodeExecutionCommand;
    /**
     * @var string
     */
    public readonly string $SourceCodeCompareCommand;
    /**
     * @var string
     */
    public readonly string $FileExecutionCommand;
    /**
     * @var string
     */
    public readonly string $FileCompareCommand;

    /**
     * @param ExecutionRuleId $executionRuleId
     * @param string $sourceCodeExecutionCommand
     * @param string $sourceCodeCompareCommand
     * @param string $fileExecutionCommand
     * @param string $fileCompareCommand
     * @return void
     */
    public function __construct(ExecutionRuleId $executionRuleId, string $sourceCodeExecutionCommand, string $sourceCodeCompareCommand, string $fileExecutionCommand, string $fileCompareCommand)
    {
        $this->ExecutionRuleId = $executionRuleId;
        $this->SourceCodeExecutionCommand = $sourceCodeExecutionCommand;
        $this->SourceCodeCompareCommand = $sourceCodeCompareCommand;
        $this->FileExecutionCommand = $fileExecutionCommand;
        $this->FileCompareCommand = $fileCompareCommand;
    }
}

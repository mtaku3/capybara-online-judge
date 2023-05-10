<?php

declare(strict_types=1);

namespace App\Domain\Problem\Factory;

use App\Domain\Common\ValueObject\Language;

class ExecutionRuleFactoryDTO
{
    public readonly Language $Language;
    public readonly string $SourceCodeExecutionCommand;
    public readonly string $SourceCodeCompareCommand;
    public readonly string $FileExecutionCommand;
    public readonly string $FileCompareCommand;

    /**
     * @param Language $language
     * @param string $sourceCodeExecutionCommand
     * @param string $sourceCodeCompareCommand
     * @param string $fileExecutionCommand
     * @param string $fileCompareCommand
     * @return void
     */
    public function __construct(Language $language, string $sourceCodeExecutionCommand, string $sourceCodeCompareCommand, string $fileExecutionCommand, string $fileCompareCommand)
    {
        $this->Language = $language;
        $this->SourceCodeExecutionCommand = $sourceCodeExecutionCommand;
        $this->SourceCodeCompareCommand = $sourceCodeCompareCommand;
        $this->FileExecutionCommand = $fileExecutionCommand;
        $this->FileCompareCommand = $fileCompareCommand;
    }
}

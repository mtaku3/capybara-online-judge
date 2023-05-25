<?php

declare(strict_types=1);

namespace App\Application\CreateProblem\DTO;

use App\Domain\Common\ValueObject\Language;

class ExecutionRuleDTO
{
    /**
     * @var Language
     */
    public readonly Language $Language;
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

<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages\DTO;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\TestCaseId;

class ExecutionRuleDTO
{
    /**
     * @var TestCaseId
     */
    public readonly TestCaseId $TestCaseId;
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
     * @param TestCaseId $testCaseId
     * @param Language $language
     * @param string $sourceCodeExecutionCommand
     * @param string $sourceCodeCompareCommand
     * @param string $fileExecutionCommand
     * @param string $fileCompareCommand
     * @return void
     */
    public function __construct(TestCaseId $testCaseId, Language $language, string $sourceCodeExecutionCommand, string $sourceCodeCompareCommand, string $fileExecutionCommand, string $fileCompareCommand)
    {
        $this->TestCaseId = $testCaseId;
        $this->Language = $language;
        $this->SourceCodeExecutionCommand = $sourceCodeExecutionCommand;
        $this->SourceCodeCompareCommand = $sourceCodeCompareCommand;
        $this->FileExecutionCommand = $fileExecutionCommand;
        $this->FileCompareCommand = $fileCompareCommand;
    }
}

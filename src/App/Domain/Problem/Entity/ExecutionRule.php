<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ExecutionRuleId;

class ExecutionRule
{
    private ExecutionRuleId $Id;
    private Language $Language;
    private string $SourceCodeExecutionCommand;
    private string $SourceCodeCompareCommand;
    private string $FileExecutionCommand;
    private string $FileCompareCommand;

    /**
     * @param ExecutionRuleId $id
     * @param Language $language
     * @param string $sourceCodeExecutionCommand
     * @param string $sourceCodeCompareCommand
     * @param string $fileExecutionCommand
     * @param string $fileCompareCommand
     * @return void
     */
    public function __construct(ExecutionRuleId $id, Language $language, string $sourceCodeExecutionCommand, string $sourceCodeCompareCommand, string $fileExecutionCommand, string $fileCompareCommand)
    {
        $this->Id = $id;
        $this->Language = $language;
        $this->SourceCodeExecutionCommand = $sourceCodeExecutionCommand;
        $this->SourceCodeCompareCommand = $sourceCodeCompareCommand;
        $this->FileExecutionCommand = $fileExecutionCommand;
        $this->FileCompareCommand = $fileCompareCommand;
    }

    /**
     * @param Language $language
     * @param string $sourceCodeExecutionCommand
     * @param string $sourceCodeCompareCommand
     * @param string $fileExecutionCommand
     * @param string $fileCompareCommand
     * @return ExecutionRule
     */
    public static function _create(Language $language, string $sourceCodeExecutionCommand, string $sourceCodeCompareCommand, string $fileExecutionCommand, string $fileCompareCommand): ExecutionRule
    {
        return new ExecutionRule(ExecutionRuleId::NextIdentity(), $language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
    }

    /** @return ExecutionRuleId  */
    public function getId(): ExecutionRuleId
    {
        return $this->Id;
    }

    /** @return Language  */
    public function getLanguage(): Language
    {
        return $this->Language;
    }

    /** @return string  */
    public function getSourceCodeExecutionCommand(): string
    {
        return $this->SourceCodeExecutionCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setSourceCodeExecutionCommand(string $command)
    {
        $this->SourceCodeExecutionCommand = $command;
    }

    /** @return string  */
    public function getSourceCodeCompareCommand(): string
    {
        return $this->SourceCodeCompareCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setSourceCodeCompareCommand(string $command)
    {
        $this->SourceCodeCompareCommand = $command;
    }

    /** @return string  */
    public function getFileExecutionCommand(): string
    {
        return $this->FileExecutionCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setFileExecutionCommand(string $command)
    {
        $this->FileExecutionCommand = $command;
    }

    /** @return string  */
    public function getFileCompareCommand(): string
    {
        return $this->FileCompareCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setFileCompareCommand(string $command)
    {
        $this->FileCompareCommand = $command;
    }
}

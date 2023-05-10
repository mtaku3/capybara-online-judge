<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\CompileRuleId;

class CompileRule
{
    private CompileRuleId $Id;
    private Language $Language;
    private string $SourceCodeCompileCommand;
    private string $FileCompileCommand;

    /**
     * @param CompileRuleId $id
     * @param Language $language
     * @param string $sourceCodeCompileCommand
     * @param string $fileCompileCommand
     * @return void
     */
    public function __construct(CompileRuleId $id, Language $language, string $sourceCodeCompileCommand, string $fileCompileCommand)
    {
        $this->Id = $id;
        $this->Language = $language;
        $this->SourceCodeCompileCommand = $sourceCodeCompileCommand;
        $this->FileCompileCommand = $fileCompileCommand;
    }

    /** @return CompileRuleId  */
    public function getId(): CompileRuleId
    {
        return $this->Id;
    }

    /** @return Language  */
    public function getLanguage(): Language
    {
        return $this->Language;
    }

    /** @return string  */
    public function getSourceCodeCompileCommand(): string
    {
        return $this->SourceCodeCompileCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setSourceCodeCompileCommand(string $command)
    {
        $this->SourceCodeCompileCommand = $command;
    }

    /** @return string  */
    public function getFileCompileCommand(): string
    {
        return $this->FileCompileCommand;
    }

    /**
     * @param string $command
     * @return void
     */
    public function _setFileCompileCommand(string $command)
    {
        $this->FileCompileCommand = $command;
    }
}

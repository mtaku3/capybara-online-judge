<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Problem\ValueObject\TestCaseId;

class TestCase
{
    private TestCaseId $Id;
    private string $Title;
    private bool $IsDisabled;
    /**
     * @var array<ExecutionRule>
     */
    private array $ExecutionRules;
    private InputFile $InputFile;
    private OutputFile $OutputFile;

    /**
     * @param TestCaseId $id
     * @param string $title
     * @param bool $isDisabled
     * @param array<ExecutionRuleFactoryDTO> $executionRules
     * @param InputFile $inputFile
     * @param OutputFile $outputFile
     * @return void
     */
    public function __construct(TestCaseId $id, string $title, bool $isDisabled, array $executionRules, InputFile $inputFile, OutputFile $outputFile)
    {
        $this->Id = $id;
        $this->Title = $title;
        $this->IsDisabled = $isDisabled;
        $this->ExecutionRules = $executionRules;
        $this->InputFile = $inputFile;
        $this->OutputFile = $outputFile;
    }

    /**
     * @param string $title
     * @param bool $isDisabled
     * @param array<ExecutionRuleFactoryDTO> $executionRules
     * @return TestCase
     */
    public static function _create(string $title, bool $isDisabled, array $executionRules): TestCase
    {
        $id = TestCaseId::NextIdentity();
        return new TestCase($id, $title, $isDisabled, $executionRules, InputFile::_create($id), OutputFile::_create($id));
    }

    /** @return TestCaseId  */
    public function getId(): TestCaseId
    {
        return $this->Id;
    }

    /** @return string  */
    public function getTitle(): string
    {
        return $this->Title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function _setTitle(string $title)
    {
        $this->Title = $title;
    }

    /** @return bool  */
    public function getIsDisabled(): bool
    {
        return $this->IsDisabled;
    }

    /** @return void  */
    public function _enable()
    {
        $this->IsDisabled = false;
    }

    /** @return void  */
    public function _disable()
    {
        $this->IsDisabled = true;
    }

    /** @return array<ExecutionRule>  */
    public function getExecutionRules(): array
    {
        return $this->ExecutionRules;
    }

    /**
     * @param array<ExecutionRule> $executionRules
     * @return void
     */
    public function _setExecutionRules(array $executionRules): void
    {
        $this->ExecutionRules = $executionRules;
    }

    /** @return InputFile  */
    public function getInputFile(): InputFile
    {
        return $this->InputFile;
    }

    /** @return OutputFile  */
    public function getOutputFile(): OutputFile
    {
        return $this->OutputFile;
    }
}

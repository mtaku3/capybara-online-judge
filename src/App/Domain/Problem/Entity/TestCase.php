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

    /**
     * @param TestCaseId $id
     * @param string $title
     * @param bool $isDisabled
     * @param array<ExecutionRuleFactoryDTO> $executionRules
     * @return void
     */
    public function __construct(TestCaseId $id, string $title, bool $isDisabled, array $executionRules)
    {
        $this->Id = $id;
        $this->Title = $title;
        $this->IsDisabled = $isDisabled;
        $this->ExecutionRules = $executionRules;
    }

    /**
     * @param string $title
     * @param bool $isDisabled
     * @param array<ExecutionRuleFactoryDTO> $executionRules
     * @return TestCase
     */
    public static function _create(string $title, bool $isDisabled, array $executionRules): TestCase
    {
        return new TestCase(TestCaseId::nextIdentity(), $title, $isDisabled, $executionRules);
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
}

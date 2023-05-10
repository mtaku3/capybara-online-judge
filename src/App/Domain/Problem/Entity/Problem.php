<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Common\Exception\CorruptedEntityException;
use App\Domain\Common\Exception\EntityNotFoundException;
use App\Domain\Common\Exception\InvalidDTOException;
use App\Domain\Problem\Exception\AtLeastOneCompileRuleRequiredException;
use App\Domain\Problem\Exception\AtLeastOneEnabledTestCaseRequiredException;
use App\Domain\Problem\Exception\InvalidMemoryConstraintException;
use App\Domain\Problem\Exception\InvalidTimeConstraintException;
use App\Domain\Problem\ValueObject\CompileRuleId;
use App\Domain\Problem\ValueObject\ExecutionRuleId;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;
use InvalidArgumentException;

class Problem
{
    public const MaxTimeConstraint = 10000;
    public const MaxMemoryConstraint = 2 * 1024 * 1024; // 2 GB in KB

    private ProblemId $Id;
    private string $Title;
    private string $Body;
    private int $TimeConstraint;
    private int $MemoryConstraint;
    /**
     * @var array<CompileRule>
     */
    private array $CompileRules;
    /**
     * @var array<TestCase>
     */
    private array $TestCases;

    /**
     * @param ProblemId $id
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param array<CompileRule> $compileRules
     * @param array<TestCase> $testCases
     * @return void
     */
    public function __construct(ProblemId $id, string $title, string $body, int $timeConstraint, int $memoryConstraint, array $compileRules, array $testCases)
    {
        $this->Id = $id;
        $this->Title = $title;
        $this->Body = $body;
        $this->TimeConstraint = $timeConstraint;
        $this->MemoryConstraint = $memoryConstraint;
        $this->CompileRules = $compileRules;
        $this->TestCases = $testCases;
    }

    /**
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param array $compileRuleDTOs
     * @param array $testCaseDTOs
     * @return Problem
     * @throws InvalidTimeConstraintException
     * @throws InvalidMemoryConstraintException
     * @throws AtLeastOneCompileRuleRequiredException
     * @throws AtLeastOneEnabledTestCaseRequiredException
     * @throws InvalidDTOException
     */
    public static function _create(string $title, string $body, int $timeConstraint, int $memoryConstraint, array $compileRuleDTOs, array $testCaseDTOs): Problem
    {
        if ($timeConstraint <= 0 && self::MaxTimeConstraint < $timeConstraint) {
            throw new InvalidTimeConstraintException();
        }

        if ($memoryConstraint <= 0 && self::MaxMemoryConstraint < $memoryConstraint) {
            throw new InvalidMemoryConstraintException();
        }

        if (empty($compileRuleDTOs)) {
            throw new AtLeastOneCompileRuleRequiredException();
        }

        if (empty($testCaseDTOs)) {
            throw new AtLeastOneEnabledTestCaseRequiredException();
        }

        $availableLanguages = [];
        $compileRules = [];
        foreach ($compileRuleDTOs as $compileRuleDTO) {
            if (array_search($compileRuleDTO->Language, $availableLanguages) !== false) {
                throw new InvalidDTOException();
            }

            $availableLanguages[] = $compileRuleDTO->Language;
            $compileRules[] = new CompileRule(
                CompileRuleId::nextIdentity(),
                $compileRuleDTO->Language,
                $compileRuleDTO->SourceCodeCompileCommand,
                $compileRuleDTO->FileCompileCommand
            );
        }

        $testCases = [];
        foreach ($testCaseDTOs as $testCaseDTO) {
            if (count($availableLanguages) !== count($testCaseDTO->ExecutionRuleDTOs)) {
                throw new InvalidDTOException();
            }

            $requiredLanguages = $availableLanguages;
            $executionRules = [];
            foreach ($testCaseDTO->ExecutionRuleDTOs as $executionRuleDTO) {
                if (current(array_filter($requiredLanguages, fn ($e) => $e === $executionRuleDTO->Language)) === false) {
                    throw new InvalidDTOException();
                }

                $requiredLanguages = array_filter($requiredLanguages, fn ($e) => $e !== $executionRuleDTO->Language);
                $executionRules[] = new ExecutionRule(
                    ExecutionRuleId::nextIdentity(),
                    $executionRuleDTO->Language,
                    $executionRuleDTO->SourceCodeExecutionCommand,
                    $executionRuleDTO->SourceCodeCompareCommand,
                    $executionRuleDTO->FileExecutionCommand,
                    $executionRuleDTO->FileCompareCommand
                );
            }

            $testCases[] = new TestCase(
                TestCaseId::nextIdentity(),
                $testCaseDTO->Title,
                false,
                $executionRules
            );
        }

        return new Problem(ProblemId::nextIdentity(), $title, $body, $timeConstraint, $memoryConstraint, $compileRules, $testCases);
    }

    /** @return ProblemId  */
    public function getId(): ProblemId
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
    public function setTitle(string $title)
    {
        $this->Title = $title;
    }

    /** @return string  */
    public function getBody(): string
    {
        return $this->Body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->Body = $body;
    }

    /** @return int  */
    public function getTimeConstraint(): int
    {
        return $this->TimeConstraint;
    }

    /** @return int  */
    public function getMemoryConstraint(): int
    {
        return $this->MemoryConstraint;
    }


    /** @return array<CompileRule>  */
    public function getCompileRules(): array
    {
        return $this->CompileRules;
    }


    /**
     * @param array<CompileRuleDTO> $compileRuleDTOs
     * @param array<ExecutionRuleDTOWithTestCaseId> $executionRuleDTOs
     * @return void
     * @throws InvalidDTOException
     */
    public function createCompileRules(array $compileRuleDTOs, array $executionRuleDTOs): void
    {
        $availableLanguages = array_map(fn ($e) => $e->getLanguage(), $this->CompileRules);
        $newlyAddedLanguages = [];

        $newCompileRules = [];
        foreach ($compileRuleDTOs as $compileRuleDTO) {
            if (array_search($compileRuleDTO->Language, $availableLanguages) !== false) {
                throw new InvalidDTOException();
            }

            $availableLanguages[] = $compileRuleDTO->Language;
            $newlyAddedLanguages[] = $compileRuleDTO->Language;
            $newCompileRules[] = new CompileRule(
                CompileRuleId::nextIdentity(),
                $compileRuleDTO->Language,
                $compileRuleDTO->SourceCodeCompileCommand,
                $compileRuleDTO->FileCompileCommand
            );
        }

        $newExecutionRules = [];
        foreach ($this->TestCases as $testCase) {
            $executionRuleDTOsForTestCase = array_filter($executionRuleDTOs, fn ($e) => $e->TestCaseId->equals($testCase->getId()));
            if (empty($executionRuleDTOsForTestCase) || count($executionRuleDTOsForTestCase) !== count($newlyAddedLanguages)) {
                throw new InvalidDTOException();
            }

            $requiredLanguages = $newlyAddedLanguages;
            $executionRules = $testCase->getExecutionRules();
            foreach ($executionRuleDTOsForTestCase as $executionRuleDTO) {
                if (array_filter($requiredLanguages, fn ($e) => $e === $executionRuleDTO->Language) === false) {
                    throw new InvalidDTOException();
                }

                $requiredLanguages = array_filter($requiredLanguages, fn ($e) => $e !== $executionRuleDTO->Language);
                $executionRules[] = new ExecutionRule(
                    ExecutionRuleId::nextIdentity(),
                    $executionRuleDTO->Language,
                    $executionRuleDTO->SourceCodeExecutionCommand,
                    $executionRuleDTO->SourceCodeCompareCommand,
                    $executionRuleDTO->FileExecutionCommand,
                    $executionRuleDTO->FileCompareCommand
                );
            }

            $newExecutionRules[(string)$testCase->getId()] = $executionRules;
        }

        $this->CompileRules = array_merge($this->CompileRules, $newCompileRules);
        foreach ($this->TestCases as $testCase) {
            $testCase->_setExecutionRules($newExecutionRules[(string)$testCase->getId()]);
        }
    }

    /**
     * @param CompileRuleId $compileRuleId
     * @return void
     * @throws AtLeastOneEnabledTestCaseRequiredException
     * @throws InvalidArgumentException
     * @throws CorruptedEntityException
     */
    public function removeCompileRule(CompileRuleId $compileRuleId): void
    {
        if (count($this->CompileRules) === 1) {
            throw new AtLeastOneEnabledTestCaseRequiredException();
        }

        $compileRule = current(array_filter($this->CompileRules, fn ($e) => $e->getId()->equals($compileRuleId)));
        if ($compileRule === false) {
            throw new InvalidArgumentException();
        }

        $newExecutionRules = [];
        foreach ($this->TestCases as $testCase) {
            $executionRules = $testCase->getExecutionRules();
            $executionRule = current(array_filter($executionRules, fn ($e) => $e->getLanguage() === $compileRule->getLanguage()));
            if ($executionRule === false) {
                throw new CorruptedEntityException();
            }

            $newExecutionRules[(string)$testCase->getId()] = array_filter($executionRules, fn ($e) => $e !== $executionRule);
        }

        $this->CompileRules = array_filter($this->CompileRules, fn ($e) => $e !== $compileRule);
        foreach ($this->TestCases as $testCase) {
            $testCase->_setExecutionRules($newExecutionRules[(string)$testCase->getId()]);
        }
    }

    /**
     * @param CompileRuleId $compileRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setCompileRuleSourceCodeCompileCommand(CompileRuleId $compileRuleId, string $command): void
    {
        if ($compileRule = current(array_filter($this->CompileRules, fn ($e) => $e->getId()->equals($compileRuleId)))) {
            $compileRule->_setSourceCodeCompileCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param CompileRuleId $compileRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setCompileRuleFileCompileCommand(CompileRuleId $compileRuleId, string $command): void
    {
        if ($compileRule = current(array_filter($this->CompileRules, fn ($e) => $e->getId()->equals($compileRuleId)))) {
            $compileRule->_setFileCompileCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }


    /** @return array<TestCase>  */
    public function getTestCases(): array
    {
        return $this->TestCases;
    }

    /**
     * @param string $title
     * @param array<ExecutionRuleDTO> $executionRuleDTOs
     * @return void
     * @throws InvalidDTOException
     */
    public function createTestCase(string $title, array $executionRuleDTOs): void
    {
        $requiredLanguages = array_map(fn ($e) => $e->getLanguage(), $this->CompileRules);
        if (count($requiredLanguages) !== count($executionRuleDTOs)) {
            throw new InvalidDTOException();
        }

        $executionRules = [];
        foreach ($executionRuleDTOs as $executionRuleDTO) {
            if (current(array_filter($requiredLanguages, fn ($e) => $e === $executionRuleDTO->Language)) === false) {
                throw new InvalidDTOException();
            }

            $requiredLanguages = array_filter($requiredLanguages, fn ($e) => $e !== $executionRuleDTO->Language);
            $executionRules[] = new ExecutionRule(
                ExecutionRuleId::nextIdentity(),
                $executionRuleDTO->Language,
                $executionRuleDTO->SourceCodeExecutionCommand,
                $executionRuleDTO->SourceCodeCompareCommand,
                $executionRuleDTO->FileExecutionCommand,
                $executionRuleDTO->FileCompareCommand
            );
        }

        $this->TestCases[] = new TestCase(TestCaseId::nextIdentity(), $title, false, $executionRules);
    }

    /**
     * @param TestCaseId $testCaseId
     * @param string $title
     * @return void
     * @throws EntityNotFoundException
     */
    public function setTestCaseTitle(TestCaseId $testCaseId, string $title): void
    {
        if ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) {
            $testCase->_setTitle($title);
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param TestCaseId $testCaseId
     * @return void
     * @throws EntityNotFoundException
     */
    public function enableTestCase(TestCaseId $testCaseId): void
    {
        if ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) {
            $testCase->_enable();
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param TestCaseId $testCaseId
     * @return void
     * @throws EntityNotFoundException
     */
    public function disableTestCase(TestCaseId $testCaseId): void
    {
        if ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) {
            $testCase->_disable();
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param TestCaseId $testCaseId
     * @param ExecutionRuleId $executionRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setExecutionRuleSourceCodeExecutionCommand(TestCaseId $testCaseId, ExecutionRuleId $executionRuleId, string $command): void
    {
        if (
            ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) &&
            ($executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getId()->equals($executionRuleId))))
        ) {
            $executionRule->_setSourceCodeExecutionCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param TestCaseId $testCaseId
     * @param ExecutionRuleId $executionRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setExecutionRuleSourceCodeCompareCommand(TestCaseId $testCaseId, ExecutionRuleId $executionRuleId, string $command): void
    {
        if (
            ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) &&
            ($executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getId()->equals($executionRuleId))))
        ) {
            $executionRule->_setSourceCodeCompareCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }


    /**
     * @param TestCaseId $testCaseId
     * @param ExecutionRuleId $executionRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setExecutionRuleFileExecutionCommand(TestCaseId $testCaseId, ExecutionRuleId $executionRuleId, string $command): void
    {
        if (
            ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) &&
            ($executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getId()->equals($executionRuleId))))
        ) {
            $executionRule->_setFileExecutionCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }

    /**
     * @param TestCaseId $testCaseId
     * @param ExecutionRuleId $executionRuleId
     * @param string $command
     * @return void
     * @throws EntityNotFoundException
     */
    public function setExecutionRuleFileCompareCommand(TestCaseId $testCaseId, ExecutionRuleId $executionRuleId, string $command): void
    {
        if (
            ($testCase = current(array_filter($this->TestCases, fn ($e) => $e->getId()->equals($testCaseId)))) &&
            ($executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getId()->equals($executionRuleId))))
        ) {
            $executionRule->_setFileCompareCommand($command);
        } else {
            throw new EntityNotFoundException();
        }
    }
}

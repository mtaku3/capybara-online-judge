<?php

declare(strict_types=1);

namespace App\Domain\Problem\Entity;

use App\Domain\Common\Exception\CorruptedEntityException;
use App\Domain\Common\Exception\EntityNotFoundException;
use App\Domain\Common\Exception\InvalidDTOException;
use App\Domain\Problem\Exception\AtLeastOneCompileRuleRequiredException;
use App\Domain\Problem\Exception\AtLeastOneEnabledTestCaseRequiredException;
use App\Domain\Problem\Exception\DuplicateTitleOfTestCasesException;
use App\Domain\Problem\Exception\InvalidMemoryConstraintException;
use App\Domain\Problem\Exception\InvalidTimeConstraintException;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTOWithTestCaseId;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use App\Domain\Problem\ValueObject\CompileRuleId;
use App\Domain\Problem\ValueObject\ExecutionRuleId;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;
use DateTimeImmutable;
use InvalidArgumentException;

class Problem
{
    public const MaxTimeConstraint = 10000;
    public const MaxMemoryConstraint = 2 * 1024 * 1024; // 2 GB in KB

    /**
     * @var ProblemId
     */
    private ProblemId $Id;
    /**
     * @var string
     */
    private string $Title;
    /**
     * @var string
     */
    private string $Body;
    /**
     * @var int
     */
    private int $TimeConstraint;
    /**
     * @var int
     */
    private int $MemoryConstraint;
    /**
     * @var CompileRule[]
     */
    private array $CompileRules;
    /**
     * @var TestCase[]
     */
    private array $TestCases;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $CreatedAt;

    /**
     * @param ProblemId $id
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param CompileRule[] $compileRules
     * @param TestCase[] $testCases
     * @param DateTimeImmutable $createdAt
     * @return void
     */
    public function __construct(ProblemId $id, string $title, string $body, int $timeConstraint, int $memoryConstraint, array $compileRules, array $testCases, DateTimeImmutable $createdAt)
    {
        $this->Id = $id;
        $this->Title = $title;
        $this->Body = $body;
        $this->TimeConstraint = $timeConstraint;
        $this->MemoryConstraint = $memoryConstraint;
        $this->CompileRules = $compileRules;
        $this->TestCases = $testCases;
        $this->CreatedAt = $createdAt;
    }

    /**
     * @param string $title
     * @param string $body
     * @param int $timeConstraint
     * @param int $memoryConstraint
     * @param CompileRuleFactoryDTO[] $compileRuleDTOs
     * @param TestCaseFactoryDTO[] $testCaseDTOs
     * @return Problem
     * @throws InvalidTimeConstraintException
     * @throws InvalidMemoryConstraintException
     * @throws AtLeastOneCompileRuleRequiredException
     * @throws AtLeastOneEnabledTestCaseRequiredException
     * @throws InvalidDTOException
     */
    public static function Create(string $title, string $body, int $timeConstraint, int $memoryConstraint, array $compileRuleDTOs, array $testCaseDTOs): Problem
    {
        if ($timeConstraint <= 0 || self::MaxTimeConstraint < $timeConstraint) {
            throw new InvalidTimeConstraintException();
        }

        if ($memoryConstraint <= 0 || self::MaxMemoryConstraint < $memoryConstraint) {
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
            $compileRules[] = CompileRule::_create(
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

            if (1 < count(array_filter($testCaseDTOs, fn ($e) => $e->Title === $testCaseDTO->Title))) {
                throw new DuplicateTitleOfTestCasesException();
            }

            $requiredLanguages = $availableLanguages;
            $executionRules = [];
            foreach ($testCaseDTO->ExecutionRuleDTOs as $executionRuleDTO) {
                if (current(array_filter($requiredLanguages, fn ($e) => $e === $executionRuleDTO->Language)) === false) {
                    throw new InvalidDTOException();
                }

                $requiredLanguages = array_filter($requiredLanguages, fn ($e) => $e !== $executionRuleDTO->Language);
                $executionRules[] = ExecutionRule::_create(
                    $executionRuleDTO->Language,
                    $executionRuleDTO->SourceCodeExecutionCommand,
                    $executionRuleDTO->SourceCodeCompareCommand,
                    $executionRuleDTO->FileExecutionCommand,
                    $executionRuleDTO->FileCompareCommand
                );
            }

            $testCases[] = TestCase::_create(
                $testCaseDTO->Title,
                false,
                $executionRules
            );
        }

        return new Problem(ProblemId::NextIdentity(), $title, $body, $timeConstraint, $memoryConstraint, $compileRules, $testCases, new DateTimeImmutable());
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


    /** @return CompileRule[]  */
    public function getCompileRules(): array
    {
        return $this->CompileRules;
    }


    /**
     * @param CompileRuleFactoryDTO[] $compileRuleDTOs
     * @param ExecutionRuleFactoryDTOWithTestCaseId[] $executionRuleDTOs
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
            $newCompileRules[] = CompileRule::_create(
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
                if (current(array_filter($requiredLanguages, fn ($e) => $e === $executionRuleDTO->Language)) === false) {
                    throw new InvalidDTOException();
                }

                $requiredLanguages = array_filter($requiredLanguages, fn ($e) => $e !== $executionRuleDTO->Language);
                $executionRules[] = ExecutionRule::_create(
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
     * @throws AtLeastOneCompileRuleRequiredException
     * @throws InvalidArgumentException
     * @throws CorruptedEntityException
     */
    public function removeCompileRule(CompileRuleId $compileRuleId): void
    {
        if (count($this->CompileRules) === 1) {
            throw new AtLeastOneCompileRuleRequiredException();
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


    /** @return TestCase[]  */
    public function getTestCases(): array
    {
        return $this->TestCases;
    }

    /**
     * @param string $title
     * @param ExecutionRuleFactoryDTO[] $executionRuleDTOs
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
            $executionRules[] = ExecutionRule::_create(
                $executionRuleDTO->Language,
                $executionRuleDTO->SourceCodeExecutionCommand,
                $executionRuleDTO->SourceCodeCompareCommand,
                $executionRuleDTO->FileExecutionCommand,
                $executionRuleDTO->FileCompareCommand
            );
        }

        if (1 <= count(array_filter($this->TestCases, fn ($e) => $e->getTitle() === $title))) {
            throw new DuplicateTitleOfTestCasesException();
        }

        $this->TestCases; // required to retrieve the array before appending : CycleORM Proxy limitation
        $this->TestCases[] = TestCase::_create($title, false, $executionRules);
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
        if (count(array_filter($this->TestCases, fn ($e) => !$e->getIsDisabled())) === 1) {
            throw new AtLeastOneEnabledTestCaseRequiredException();
        }

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

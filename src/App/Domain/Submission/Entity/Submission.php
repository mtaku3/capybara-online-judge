<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\Exception\AlreadyJudgedException;
use App\Domain\Submission\Exception\TestResultForGivenTestCaseAlreadyExistsException;
use App\Domain\Submission\ValueObject\SubmissionJudgeResult;
use App\Domain\Submission\ValueObject\SubmissionType;
use App\Domain\Submission\ValueObject\TestResultJudgeResult;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

class Submission
{
    /**
     * @var SubmissionId
     */
    private SubmissionId $Id;
    /**
     * @var UserId
     */
    private UserId $UserId;
    /**
     * @var ProblemId
     */
    private ProblemId $ProblemId;
    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $SubmittedAt;
    /**
     * @var Language
     */
    private Language $Language;
    /**
     * @var int
     */
    private int $CodeLength;
    /**
     * @var SubmissionJudgeResult
     */
    private SubmissionJudgeResult $JudgeResult;
    /**
     * @var null|int
     */
    private ?int $ExecutionTime;
    /**
     * @var null|int
     */
    private ?int $ConsumedMemory;
    /**
     * @var TestResult[]
     */
    private array $TestResults;
    /**
     * @var SubmissionType
     */
    private SubmissionType $SubmissionType;
    /**
     * @var SourceFile
     */
    private SourceFile $SourceFile;

    /**
     * @param SubmissionId $id
     * @param UserId $sserId
     * @param ProblemId $problemId
     * @param DateTimeImmutable $submittedAt
     * @param Language $language
     * @param int $codeLength
     * @param SubmissionJudgeResult $judgeResult
     * @param null|int $executionTime
     * @param null|int $consumedMemory
     * @param TestResultFactoryDTO[] $testResults
     * @param SubmissionType $submissionType
     * @param SourceFile $sourceFile
     * @return void
     */
    public function __construct(SubmissionId $id, UserId $userId, ProblemId $problemId, DateTimeImmutable $submittedAt, Language $language, int $codeLength, SubmissionJudgeResult $judgeResult, ?int $executionTime, ?int $consumedMemory, array $testResults, SubmissionType $submissionType, SourceFile $sourceFile)
    {
        $this->Id = $id;
        $this->UserId = $userId;
        $this->ProblemId = $problemId;
        $this->SubmittedAt = $submittedAt;
        $this->Language = $language;
        $this->CodeLength = $codeLength;
        $this->JudgeResult = $judgeResult;
        $this->ExecutionTime = $executionTime;
        $this->ConsumedMemory = $consumedMemory;
        $this->TestResults = $testResults;
        $this->SubmissionType = $submissionType;
        $this->SourceFile = $sourceFile;
    }

    /**
     * @param User $user
     * @param Problem $problem
     * @param Language $language
     * @param int $codeLength
     * @param SubmissionType $submissionType
     * @return Submission
     */
    public static function Create(User $user, Problem $problem, Language  $language, SubmissionType $submissionType, int $codeLength): Submission
    {
        $id = SubmissionId::NextIdentity();
        return new Submission($id, $user->getId(), $problem->getId(), new DateTimeImmutable(), $language, $codeLength, SubmissionJudgeResult::WJ, null, null, [], $submissionType, SourceFile::_create($id));
    }

    /** @return SubmissionId  */
    public function getId(): SubmissionId
    {
        return $this->Id;
    }

    /** @return UserId  */
    public function getUserId(): UserId
    {
        return $this->UserId;
    }

    /** @return ProblemId  */
    public function getProblemId(): ProblemId
    {
        return $this->ProblemId;
    }

    /** @return DateTimeImmutable  */
    public function getSubmittedAt(): DateTimeImmutable
    {
        return $this->SubmittedAt;
    }

    /** @return Language  */
    public function getLanguage(): Language
    {
        return $this->Language;
    }

    /** @return int  */
    public function getCodeLength(): int
    {
        return $this->CodeLength;
    }

    /** @return JudgeResult  */
    public function getJudgeResult(): SubmissionJudgeResult
    {
        return $this->JudgeResult;
    }

    /**
     * @return void
     * @throws AlreadyJudgedException
     */
    public function completeJudge(): void
    {
        if ($this->JudgeResult !== SubmissionJudgeResult::WJ) {
            throw new AlreadyJudgedException();
        }

        if (empty($this->TestResults)) {
            $this->JudgeResult = SubmissionJudgeResult::CE;
        } else {
            $maxJudgeResult = $this->TestResults[0]->getJudgeResult();
            $maxExecutionTime = $this->TestResults[0]->getExecutionTime();
            $maxConsumedMemory = $this->TestResults[0]->getConsumedMemory();
            for ($i = 1; $i < count($this->TestResults); $i++) {
                if ($maxJudgeResult->compares($this->TestResults[$i]->getJudgeResult()) < 0) {
                    $maxJudgeResult = $this->TestResults[$i]->getJudgeResult();
                }
                $maxExecutionTime = max([$maxExecutionTime, $this->TestResults[$i]->getExecutionTime()]);
                $maxConsumedMemory = max([$maxConsumedMemory, $this->TestResults[$i]->getConsumedMemory()]);
            }

            $this->JudgeResult = SubmissionJudgeResult::Cast($maxJudgeResult);
            $this->ExecutionTime = $maxExecutionTime;
            $this->ConsumedMemory = $maxConsumedMemory;
        }
    }

    /** @return int  */
    public function getExecutionTime(): int
    {
        return $this->ExecutionTime;
    }

    /** @return int  */
    public function getConsumedMemory(): int
    {
        return $this->ConsumedMemory;
    }

    /** @return TestResult[]  */
    public function getTestResults(): array
    {
        return $this->TestResults;
    }

    /**
     * @param Problem $problem
     * @param TestCaseId $testCaseId
     * @param bool $wrongAnswer
     * @param bool $hasRuntimeErrorOccurred
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     * @throws InvalidArgumentException
     * @throws TestResultForGivenTestCaseAlreadyExistsException
     */
    public function createTestResult(Problem $problem, TestCaseId $testCaseId, bool $wrongAnswer, bool $hasRuntimeErrorOccurred, int $executionTime, int $consumedMemory): void
    {
        if (!$problem->getId()->equals($this->ProblemId)) {
            throw new InvalidArgumentException();
        }

        if (current(array_filter($problem->getTestCases(), fn ($e) => $e->getId()->equals($testCaseId))) === false) {
            throw new InvalidArgumentException();
        }

        if (current(array_filter($this->TestResults, fn ($e) => $e->getTestCaseId()->equals($testCaseId))) !== false) {
            throw new TestResultForGivenTestCaseAlreadyExistsException();
        }

        if ($hasRuntimeErrorOccurred) {
            $judgeResult = TestResultJudgeResult::RE;
        } elseif ($problem->getTimeConstraint() < $executionTime) {
            $judgeResult = TestResultJudgeResult::TLE;
        } elseif ($problem->getMemoryConstraint() < $consumedMemory) {
            $judgeResult = TestResultJudgeResult::MLE;
        } elseif ($wrongAnswer) {
            $judgeResult = TestResultJudgeResult::WA;
        } else {
            $judgeResult = TestResultJudgeResult::AC;
        }


        $testResult = TestResult::_create($this->Id, $testCaseId, $judgeResult, $executionTime, $consumedMemory);
        $this->TestResults; // required to retrieve the array before appending : CycleORM Proxy limitation
        $this->TestResults[] = $testResult;
    }

    /** @return SubmissionType  */
    public function getSubmissionType(): SubmissionType
    {
        return $this->SubmissionType;
    }

    /** @return SourceFile  */
    public function getSourceFile(): SourceFile
    {
        return $this->SourceFile;
    }
}

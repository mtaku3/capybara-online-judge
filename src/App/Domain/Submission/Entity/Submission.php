<?php

declare(strict_types=1);

namespace App\Domain\Submission\Entity;

use App\Domain\Common\ValueObject\JudgeResult;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\Exception\AlreadyJudgedException;
use App\Domain\Submission\Exception\TestResultForGivenTestCaseAlreadyExistsException;
use App\Domain\Submission\ValueObject\TestResultId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use InvalidArgumentException;

class Submission
{
    private SubmissionId $Id;
    private UserId $UserId;
    private ProblemId $ProblemId;
    private DateTimeImmutable $SubmittedAt;
    private Language $Language;
    private int $CodeLength;
    private JudgeResult $JudgeResult;
    private ?int $ExecutionTime;
    private ?int $ConsumedMemory;
    /**
     * @var array<TestResult>
     */
    private array $TestResults;

    /**
     * @param SubmissionId $id
     * @param UserId $sserId
     * @param ProblemId $problemId
     * @param DateTimeImmutable $submittedAt
     * @param Language $language
     * @param int $codeLength
     * @param JudgeResult $judgeResult
     * @param null|int $executionTime
     * @param null|int $consumedMemory
     * @param array $testResults
     * @return void
     */
    public function __construct(SubmissionId $id, UserId $userId, ProblemId $problemId, DateTimeImmutable $submittedAt, Language $language, int $codeLength, JudgeResult $judgeResult, ?int $executionTime, ?int $consumedMemory, array $testResults)
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
    }

    /**
     * @param UserId $userId
     * @param ProblemId $problemId
     * @param Language $language
     * @param int $codeLength
     * @return Submission
     */
    public static function _create(UserId $userId, ProblemId $problemId, Language  $language, int $codeLength): Submission
    {
        return new Submission(SubmissionId::nextIdentity(), $userId, $problemId, new DateTimeImmutable(), $language, $codeLength, JudgeResult::WJ, null, null, []);
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
    public function getJudgeResult(): JudgeResult
    {
        return $this->JudgeResult;
    }

    /**
     * @return void
     * @throws AlreadyJudgedException
     */
    public function completeJudge(): void
    {
        if ($this->JudgeResult !== JudgeResult::WJ) {
            throw new AlreadyJudgedException();
        }

        if (empty($this->TestResults)) {
            $this->JudgeResult = JudgeResult::CE;
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

            $this->JudgeResult = $maxJudgeResult;
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

    /** @return array<TestResult>  */
    public function getTestResults(): array
    {
        return $this->TestResults;
    }

    /**
     * @param TestCaseId $testCaseId
     * @param JudgeResult $judgeResult
     * @param int $executionTime
     * @param int $consumedMemory
     * @return void
     * @throws TestResultForGivenTestCaseAlreadyExistsException
     */
    public function createTestResult(TestCaseId $testCaseId, JudgeResult $judgeResult, int $executionTime, int $consumedMemory): void
    {
        if ($judgeResult === JudgeResult::WJ || $judgeResult === JudgeResult::CE) {
            throw new InvalidArgumentException();
        }

        foreach ($this->TestResults as $testResult) {
            if ($testResult->getTestCaseId()->equals($testCaseId)) {
                throw new TestResultForGivenTestCaseAlreadyExistsException();
            }
        }

        $testResult = new TestResult(TestResultId::nextIdentity(), $this->Id, $testCaseId, $judgeResult, $executionTime, $consumedMemory);
        $this->TestResults[] = $testResult;
    }
}

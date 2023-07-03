<?php

declare(strict_types=1);

use App\Domain\Common\ValueObject\Language;
use App\Domain\User\Entity\User;
use App\Domain\Problem\Entity\Problem;
use PHPUnit\Framework\TestCase;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use App\Domain\Submission\ValueObject\SubmissionJudgeResult;
use App\Domain\Submission\ValueObject\SubmissionType;
use App\Domain\Submission\ValueObject\TestResultJudgeResult;

class SubmissionTest extends TestCase
{
    protected User $user;
    protected Problem $problem;

    protected function setUp(): void
    {
        $language = Language::C;
        $compileRuleDTOs = array(new CompileRuleFactoryDTO($language, 'rightComand', 'rightComand'));
        $TestCaseDTOs = [
            new TestCaseFactoryDTO("TestCase #1", [
                new ExecutionRuleFactoryDTO($language, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #2", [
                new ExecutionRuleFactoryDTO($language, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #3", [
                new ExecutionRuleFactoryDTO($language, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #4", [
                new ExecutionRuleFactoryDTO($language, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #5", [
                new ExecutionRuleFactoryDTO($language, "", "", "", "")
            ])
        ];
        $this->user = User::Create('bavoiub', 'baviuhvfeqir', false);
        $this->problem = Problem::Create('test', 'testbody', 100, 100, $compileRuleDTOs, $TestCaseDTOs);
    }

    public function test_submission_creation()
    {
        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);

        $this->assertEquals(SubmissionJudgeResult::WJ, $submission->getJudgeResult());
        $this->assertEquals(null, $submission->getExecutionTime());
        $this->assertEquals(null, $submission->getConsumedMemory());
    }

    public function test_createTestResult()
    {
        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);

        $testCases = $this->problem->getTestCases();
        $timeConstraint = $this->problem->getTimeConstraint();
        $memoryConstraint = $this->problem->getMemoryConstraint();

        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        $this->assertEquals(TestResultJudgeResult::AC, $submission->getTestResults()[0]->getJudgeResult());

        // WA
        $submission->createTestResult($this->problem, $testCases[1]->getId(), true, false, $timeConstraint - 1, $memoryConstraint - 1);
        $this->assertEquals(TestResultJudgeResult::WA, $submission->getTestResults()[1]->getJudgeResult());

        // RE
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, true, $timeConstraint - 1, $memoryConstraint - 1);
        $this->assertEquals(TestResultJudgeResult::RE, $submission->getTestResults()[2]->getJudgeResult());

        // TLE
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint + 1, $memoryConstraint - 1);
        $this->assertEquals(TestResultJudgeResult::TLE, $submission->getTestResults()[3]->getJudgeResult());

        // MLE
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint + 1);
        $this->assertEquals(TestResultJudgeResult::MLE, $submission->getTestResults()[4]->getJudgeResult());
    }

    public function test_judge_result_priority()
    {
        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);

        $testCases = $this->problem->getTestCases();
        $timeConstraint = $this->problem->getTimeConstraint();
        $memoryConstraint = $this->problem->getMemoryConstraint();

        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // WA
        $submission->createTestResult($this->problem, $testCases[1]->getId(), true, false, $timeConstraint - 1, $memoryConstraint - 1);
        // RE
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, true, $timeConstraint - 1, $memoryConstraint - 1);
        // TLE
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint + 1, $memoryConstraint - 1);
        // MLE
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint + 1);

        $submission->completeJudge();
        $this->assertEquals(SubmissionJudgeResult::MLE, $submission->getJudgeResult());


        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);
        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // WA
        $submission->createTestResult($this->problem, $testCases[1]->getId(), true, false, $timeConstraint - 1, $memoryConstraint - 1);
        // RE
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, true, $timeConstraint - 1, $memoryConstraint - 1);
        // TLE
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint + 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);

        $submission->completeJudge();
        $this->assertEquals(SubmissionJudgeResult::TLE, $submission->getJudgeResult());


        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);
        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // WA
        $submission->createTestResult($this->problem, $testCases[1]->getId(), true, false, $timeConstraint - 1, $memoryConstraint - 1);
        // RE
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, true, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);

        $submission->completeJudge();
        $this->assertEquals(SubmissionJudgeResult::RE, $submission->getJudgeResult());


        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);
        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // WA
        $submission->createTestResult($this->problem, $testCases[1]->getId(), true, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);

        $submission->completeJudge();
        $this->assertEquals(SubmissionJudgeResult::WA, $submission->getJudgeResult());


        $submission = Submission::Create($this->user, $this->problem, Language::C, SubmissionType::SourceCode, 100);
        // AC
        $submission->createTestResult($this->problem, $testCases[0]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[1]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[2]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[3]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);
        // AC
        $submission->createTestResult($this->problem, $testCases[4]->getId(), false, false, $timeConstraint - 1, $memoryConstraint - 1);

        $submission->completeJudge();
        $this->assertEquals(SubmissionJudgeResult::AC, $submission->getJudgeResult());
    }
}

<?php

declare(strict_types=1);

use App\Application\Submit\SubmitRequest;
use App\Application\Submit\SubmitUseCase;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;
use Test\Infrastructure\User\MockUserRepository;
use Test\Infrastructure\File\MockFileRepository;
use Test\Infrastructure\JudgeQueue\MockJudgeQueueRepository;
use Test\Infrastructure\Submission\MockSubmissionRepository;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Submission\ValueObject\SubmissionType;
use App\Domain\User\Entity\User;

class SubmitUseCaseTest extends TestCase
{
    protected SubmitUseCase  $submitUseCase;

    protected MockFileRepository $mockFileRepository;
    protected MockJudgeQueueRepository $mockJudgeQueueRepository;
    protected MockProblemRepository $mockProblemRepository;
    protected MockUserRepository $mockUserRepository;
    protected MockSubmissionRepository $mockSubmissionRepository;

    protected function setUp(): void
    {
        $this->mockSubmissionRepository = new MockSubmissionRepository();
        $this->mockJudgeQueueRepository = new MockJudgeQueueRepository();
        $this->mockUserRepository = new MockUserRepository();
        $this->mockProblemRepository = new MockProblemRepository();
        $this->mockFileRepository = new MockFileRepository();

        $this->submitUseCase = new SubmitUseCase($this->mockUserRepository, $this->mockProblemRepository, $this->mockFileRepository, $this->mockSubmissionRepository, $this->mockJudgeQueueRepository);
    }

    public function test(): void
    {
        $langueageC  = Language::C;
        $langueageCPP  = Language::CPP;
        $compileRuleDTOs = array(
            new CompileRuleFactoryDTO($langueageC, 'rightComand', 'rightComand'),
            new CompileRuleFactoryDTO($langueageCPP, 'rightComand', 'rightComand'),
        );
        $ExecutionRuleDTOs = array(
            new ExecutionRuleFactoryDTO($langueageC, 'right', 'right', 'right', 'right'),
            new ExecutionRuleFactoryDTO($langueageCPP, 'right', 'right', 'right', 'right'),
        );
        $testCaseDTOs = array(new TestCaseFactoryDTO('rightComand', $ExecutionRuleDTOs));

        $problem = Problem::Create('rightTitle', 'rightBoby', 1, 200, $compileRuleDTOs, $testCaseDTOs);
        $user = User::Create('aobuto', 'password', false);

        $this->mockProblemRepository->save($problem);
        $this->mockUserRepository->save($user);

        $request = new SubmitRequest($user->getId(), $problem->getId(), $langueageC, SubmissionType::File, 'aaaaaaaa');
        $response = $this->submitUseCase->handle($request);

        $submission = $response->Submission;

        $this->assertEquals($submission->getUserId(), $user->getId());
        $this->assertEquals($submission->getProblemId(), $problem->getId());
        $this->assertEquals($submission->getLanguage(), $langueageC);
        $this->assertEquals($submission->getSubmissionType(), SubmissionType::File);
        $this->assertEquals($submission, $this->mockSubmissionRepository->findById($submission->getId()));
        $this->assertEquals($submission->getId(), $this->mockJudgeQueueRepository->dequeue());
    }
}

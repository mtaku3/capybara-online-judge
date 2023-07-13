<?php

declare(strict_types=1);

use App\Application\DisableTestCase\DisableTestCaseRequest;
use App\Application\DisableTestCase\DisableTestCaseUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class DisableTestCaseUseCaseTest extends TestCase
{
    protected DisableTestCaseUseCase $disableTestCaseUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->disableTestCaseUseCase = new DisableTestCaseUseCase($this->mockProblemRepository);
    }

    public function test(): void
    {
        $compileRuleDTOs = array(new CompileRuleFactoryDTO(Language::C, 'rightComand', 'rightComand'));
        $testCaseDTOs = [
            new TestCaseFactoryDTO("TestCase #1", [
                new ExecutionRuleFactoryDTO(Language::C, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #2", [
                new ExecutionRuleFactoryDTO(Language::C, "", "", "", "")
            ])
        ];
        $problem = Problem::Create('title', 'body', 1000, 1000, $compileRuleDTOs, $testCaseDTOs);
        $problemId = $problem->getId();
        $testCaseId = $problem->getTestCases()[0]->getId();

        $this->mockProblemRepository->save($problem);

        $request = new DisableTestCaseRequest($problemId, $testCaseId);

        $response = $this->disableTestCaseUseCase->handle($request);
        $this->assertSame($response->Problem->getTestCases()[0]->getIsDisabled(), true);
    }
}

<?php

declare(strict_types=1);

use App\Application\EnableTestCase\EnableTestCaseRequest;
use App\Application\EnableTestCase\EnableTestCaseUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class EnableTestCaseTest extends TestCase
{
    protected EnableTestCaseUseCase $enableTestCaseUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->enableTestCaseUseCase = new EnableTestCaseUseCase($this->mockProblemRepository);
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
        $problem = Problem::Create('title','body',1000,1000,$compileRuleDTOs,$testCaseDTOs);
        $problemId = $problem->getId();
        $testCaseId = $problem->getTestCases()[0]->getId();
        $problem->disableTestCase($testCaseId);

        $this->mockProblemRepository->save($problem);

        $request = new EnableTestCaseRequest($problemId, $testCaseId);

        $response = $this->enableTestCaseUseCase->handle($request);
        $this->assertSame($response->Problem->getTestCases()[0]->getIsDisabled(), false);
    }
}
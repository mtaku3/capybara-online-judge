<?php

declare(strict_types=1);

use App\Application\UpdateTestCase\DTO\ExecutionRuleDTO;
use App\Application\CreateProblem\DTO\TestCaseDTO;
use App\Application\UpdateTestCase\UpdateTestCaseRequest;
use App\Application\UpdateTestCase\UpdateTestCaseUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Entity\TestCase as EntityTestCase;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use App\Domain\Problem\ValueObject\TestCaseId;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class UpdateTestCaseUseCaseTest extends TestCase
{
    protected UpdateTestCaseUseCase $updateTestCaseUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->updateTestCaseUseCase = new UpdateTestCaseUseCase($this->mockProblemRepository);
    }

    public function test(): void
    {
        $compileRuleDTOs = array(new CompileRuleFactoryDTO(Language::C, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO(Language::C, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = [
            new TestCaseFactoryDTO("TestCase #1", $ExecutionRuleDTOs),
            new TestCaseFactoryDTO("TestCase #2", $ExecutionRuleDTOs),
        ];
        $problem = Problem::Create('title', 'body', 1000, 1000, $compileRuleDTOs, $testCaseDTOs);
        $this->mockProblemRepository->save($problem);
        $problemId = $problem->getId();
        $testCases = $problem->getTestCases();
        $ExecutionRuleDTO = array(new ExecutionRuleDTO($testCases[0]->getExecutionRules()[0]->getId(), 'right1', 'right2', 'right3', 'right4'));

        $request = new UpdateTestCaseRequest($problemId, $testCases[0]->getId(), 'title1', $ExecutionRuleDTO);
        $response = $this->updateTestCaseUseCase->handle($request);
        $newproblem = $this->mockProblemRepository->findById($problemId);
        $this->assertSame($newproblem->getTestCases()[0]->getTitle(), 'title1');
        $this->assertSame($newproblem->getTestCases()[0]->getExecutionRules()[0]->getSourceCodeExecutionCommand(), $ExecutionRuleDTO[0]->SourceCodeExecutionCommand);
        $this->assertSame($newproblem->getTestCases()[0]->getExecutionRules()[0]->getSourceCodeCompareCommand(), $ExecutionRuleDTO[0]->SourceCodeCompareCommand);
        $this->assertSame($newproblem->getTestCases()[0]->getExecutionRules()[0]->getFileExecutionCommand(), $ExecutionRuleDTO[0]->FileExecutionCommand);
        $this->assertSame($newproblem->getTestCases()[0]->getExecutionRules()[0]->getFileCompareCommand(), $ExecutionRuleDTO[0]->FileCompareCommand);
    }
}

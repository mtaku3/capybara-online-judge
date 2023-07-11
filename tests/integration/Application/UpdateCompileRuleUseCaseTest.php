<?php

declare(strict_types=1);

use App\Application\UpdateCompileRule\UpdateCompileRuleRequest;
use App\Application\UpdateCompileRule\UpdateCompileRuleUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class UpdateCompileRuleUseCaseTest extends TestCase
{
    protected UpdateCompileRuleUseCase $updateCompileRuleUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->updateCompileRuleUseCase = new UpdateCompileRuleUseCase($this->mockProblemRepository);
    }

    public function test(): void
    {
        $compileRuleDTOs = array(new CompileRuleFactoryDTO(Language::C, 'rightComand', 'rightComand'));
        $ExecutionRuleDTOs = array(new ExecutionRuleFactoryDTO(Language::C, 'right', 'right', 'right', 'right'));
        $testCaseDTOs = [
            new TestCaseFactoryDTO("TestCase #1", [
                new ExecutionRuleFactoryDTO(Language::C, "", "", "", "")
            ]),
            new TestCaseFactoryDTO("TestCase #2", [
                new ExecutionRuleFactoryDTO(Language::C, "", "", "", "")
            ])
        ];
        $problem = Problem::Create('title', 'body', 1000, 1000, $compileRuleDTOs, $testCaseDTOs);
        $this->mockProblemRepository->save($problem);
        $problemId = $problem->getId();
        $compileRule = $problem->getCompileRules();

        $request = new UpdateCompileRuleRequest($problemId, $compileRule[0]->getId(), 'rightComand1', 'rightComand1');
        $response = $this->updateCompileRuleUseCase->handle($request);
        $newproblem = $this->mockProblemRepository->findById($problemId);
        $this->assertSame($newproblem->getCompileRules()[0]->getSourceCodeCompileCommand(), 'rightComand1');
        $this->assertSame($newproblem->getCompileRules()[0]->getFileCompileCommand(), 'rightComand1');
    }
}

<?php

declare(strict_types=1);

use App\Application\AddProblemLanguages\AddProblemLanguagesRequest;
use App\Application\AddProblemLanguages\AddProblemLanguagesUseCase;
use App\Application\AddProblemLanguages\DTO\CompileRuleDTO;
use App\Application\AddProblemLanguages\DTO\ExecutionRuleDTO;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class AddProblemLanguageUseCaseTest extends TestCase
{
    protected AddProblemLanguagesUseCase $addProblemLanguageUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->addProblemLanguageUseCase = new AddProblemLanguagesUseCase($this->mockProblemRepository);
    }

    public function test(): void
    {
        $compileRuleDTOs = array(new CompileRuleFactoryDTO(Language::C, 'rightComand', 'rightComand'));
        $executionRuleDTOs = array(new ExecutionRuleFactoryDTO(Language::C, "", "", "", ""));
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

        $this->mockProblemRepository->save($problem);
        $compileRuleDTOs = array(new CompileRuleDTO(Language::CPP, 'rightComand', 'rightComand'));
        $executionRuleDTOs = array_map(fn ($e) =>new ExecutionRuleDTO($e->getId(), Language::CPP, "", "", "", ""), $problem->getTestCases());

        $request = new AddProblemLanguagesRequest($problemId, $compileRuleDTOs, $executionRuleDTOs);
        $response = $this->addProblemLanguageUseCase->handle($request);
        $array_some = function ($arr, $con) {
            foreach($arr as $e) {
                if($con($e)) {
                    return true;
                }
            }
            return false;
        };
        $same_language = fn ($e) =>$e->getLanguage()==Language::CPP;
        $this->assertTrue($array_some($response->Problem->getCompileRules(), $same_language));
        $this->assertTrue($array_some($response->Problem->getTestCases()[0]->getExecutionRules(), $same_language));
        $this->assertTrue($array_some($response->Problem->getTestCases()[1]->getExecutionRules(), $same_language));
    }
}

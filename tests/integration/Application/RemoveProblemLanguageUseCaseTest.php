<?php

declare(strict_types=1);

use App\Application\RemoveProblemLanguages\RemoveProblemLanguagesRequest;
use App\Application\RemoveProblemLanguages\RemoveProblemLanguagesUseCase;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use App\Domain\Common\ValueObject\Language;
use Test\Infrastructure\Problem\MockProblemRepository;

// TODO: This should be deleted because of redundancy

class RemoveProblemLanguageUseCaseTest extends TestCase
{
    protected RemoveProblemLanguagesUseCase $removeProblemLanguagesUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->removeProblemLanguagesUseCase = new RemoveProblemLanguagesUseCase($this->mockProblemRepository);
    }


    public function test_removeProblemLanguageUseCase(): void
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

        $this->mockProblemRepository->save($problem);

        $request = new RemoveProblemLanguagesRequest($problem->getId(), array($langueageC));
        $response = $this->removeProblemLanguagesUseCase->handle($request);
        $problem = $response->Problem;

        foreach($problem->getCompileRules() as $compileRule) {
            $this->assertEquals($compileRule->getLanguage(), Language::CPP);
        }
    }
}

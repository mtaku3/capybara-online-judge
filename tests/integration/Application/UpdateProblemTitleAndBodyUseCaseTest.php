<?php

declare(strict_types=1);

use App\Application\UpdateProblemTitleAndBody\UpdateProblemTitleAndBodyRequest;
use App\Application\UpdateProblemTitleAndBody\UpdateProblemTitleAndBodyUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use PHPUnit\Framework\TestCase;
use Test\Infrastructure\Problem\MockProblemRepository;

class UpdateProblemTitleAndBodyUseCaseTest extends TestCase
{
    protected UpdateProblemTitleAndBodyUseCase $updateProblemTitleAndBodyUseCase;
    protected MockProblemRepository $mockProblemRepository;

    protected function setUp(): void
    {
        $this->mockProblemRepository = new MockProblemRepository();
        $this->updateProblemTitleAndBodyUseCase = new UpdateProblemTitleAndBodyUseCase($this->mockProblemRepository);
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
        $this->mockProblemRepository->save($problem);

        $problemId = $problem->getId();
        $request = new UpdateProblemTitleAndBodyRequest($problemId, 'title1', 'body1');
        $response =$this->updateProblemTitleAndBodyUseCase->handle($request);
        $this->assertSame($response->Problem->getTitle(), 'title1');
        $this->assertSame($response->Problem->getBody(), 'body1');
    }
}

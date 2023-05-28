<?php

declare(strict_types=1);

namespace App\Application\UpdateTestCase;

use App\Application\Exception\TestCaseNotFoundException;
use App\Domain\File\IFileRepository;
use App\Domain\Problem\IProblemRepository;

class UpdateTestCaseUseCase
{
    /**
     * @var IProblemRepository
     */
    private readonly IProblemRepository $ProblemRepository;

    /**
     * @param IProblemRepository $problemRepository
     * @return void
     */
    public function __construct(IProblemRepository $problemRepository)
    {
        $this->ProblemRepository = $problemRepository;
    }

    public function handle(UpdateTestCaseRequest $request): UpdateTestCaseResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        foreach ($request->ExecutionRuleDTOs as $executionRuleDTO) {
            $problem->setExecutionRuleSourceCodeExecutionCommand($request->TestCaseId, $executionRuleDTO->ExecutionRuleId, $executionRuleDTO->SourceCodeExecutionCommand);
            $problem->setExecutionRuleSourceCodeCompareCommand($request->TestCaseId, $executionRuleDTO->ExecutionRuleId, $executionRuleDTO->SourceCodeCompareCommand);
            $problem->setExecutionRuleFileExecutionCommand($request->TestCaseId, $executionRuleDTO->ExecutionRuleId, $executionRuleDTO->FileExecutionCommand);
            $problem->setExecutionRuleFileCompareCommand($request->TestCaseId, $executionRuleDTO->ExecutionRuleId, $executionRuleDTO->FileCompareCommand);
        }

        $this->ProblemRepository->save($problem);

        return new UpdateTestCaseResponse();
    }
}

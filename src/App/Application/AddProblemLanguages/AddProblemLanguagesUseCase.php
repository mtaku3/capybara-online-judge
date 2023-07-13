<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages;

use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTOWithTestCaseId;
use App\Domain\Problem\IProblemRepository;

class AddProblemLanguagesUseCase
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

    /**
     * @param AddProblemLanguagesRequest $request
     * @return AddProblemLanguagesResponse
     */
    public function handle(AddProblemLanguagesRequest $request): AddProblemLanguagesResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        $compileRules = [];
        foreach ($request->CompileRuleDTOs as $compileRuleDTO) {
            $language = $compileRuleDTO->Language;
            $sourceCodeCompileCommand = $compileRuleDTO->SourceCodeCompileCommand;
            $fileCompileCommand = $compileRuleDTO->FileCompileCommand;
            $compileRules[] = new CompileRuleFactoryDTO($language, $sourceCodeCompileCommand, $fileCompileCommand);
        }

        $executionRules =[];
        foreach ($request->ExecutionRuleDTOs as $executionRuleDTO) {
            $testCaseId = $executionRuleDTO->TestCaseId;
            $language = $executionRuleDTO->Language;
            $sourceCodeExecutionCommand = $executionRuleDTO->SourceCodeExecutionCommand;
            $sourceCodeCompareCommand = $executionRuleDTO->SourceCodeCompareCommand;
            $fileExcutionCommand = $executionRuleDTO->FileExecutionCommand;
            $fileCompareCommand = $executionRuleDTO->FileCompareCommand;
            $executionRules[] = new ExecutionRuleFactoryDTOWithTestCaseId($testCaseId, $language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExcutionCommand, $fileCompareCommand);
        }

        $problem->createCompileRules($compileRules, $executionRules);

        $this->ProblemRepository->save($problem);

        return new AddProblemLanguagesResponse($problem);
    }
}

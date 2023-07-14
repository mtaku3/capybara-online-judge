<?php

declare(strict_types=1);

namespace App\Application\CreateProblem;

use App\Domain\File\IFileRepository;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Factory\CompileRuleFactoryDTO;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\Factory\TestCaseFactoryDTO;
use App\Domain\Problem\IProblemRepository;

class CreateProblemUseCase
{
    /**
     * @var IProblemRepository
     */
    private readonly IProblemRepository $ProblemRepository;
    /**
     * @var IFileRepository
     */
    private readonly IFileRepository $FileRepository;

    /**
     * @param IProblemRepository $problemRepository
     * @param IFileRepository $fileRepository
     * @return void
     */
    public function __construct(IProblemRepository $problemRepository, IFileRepository $fileRepository)
    {
        $this->ProblemRepository = $problemRepository;
        $this->FileRepository = $fileRepository;
    }

    /**
     * @param CreateProblemRequest $request
     * @return CreateProblemResponse
     */
    public function handle(CreateProblemRequest $request): CreateProblemResponse
    {
        $compileRules = [];
        foreach ($request->CompileRuleDTOs as $compileRuleDTO) {
            $language = $compileRuleDTO->Language;
            $sourceCodeCompileCommand = $compileRuleDTO->SourceCodeCompileCommand;
            $fileCompileCommand = $compileRuleDTO->FileCompileCommand;
            $compileRules[] = new CompileRuleFactoryDTO($language, $sourceCodeCompileCommand, $fileCompileCommand);
        }

        $testCases = [];
        foreach ($request->TestCaseDTOs as $TestCaseDTO) {
            $title = $TestCaseDTO->Title;
            $executionRules = [];
            foreach($TestCaseDTO->ExecutionRuleDTOs as $ExecutionRuleDTO) {
                $language = $ExecutionRuleDTO->Language;
                $sourceCodeExecutionCommand = $ExecutionRuleDTO->SourceCodeExecutionCommand;
                $sourceCodeCompareCommand = $ExecutionRuleDTO->SourceCodeCompareCommand;
                $fileExecutionCommand = $ExecutionRuleDTO->FileExecutionCommand;
                $fileCompareCommand = $ExecutionRuleDTO->FileCompareCommand;
                $executionRules[] = new ExecutionRuleFactoryDTO($language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
            }
            $testCases[] = new TestCaseFactoryDTO($title, $executionRules);
        }

        $problem = Problem::Create($request->Title, $request->Body, $request->TimeConstraint, $request->MemoryConstraint, $compileRules, $testCases);

        foreach ($request->TestCaseDTOs as $idx => $testCaseDTO) {
            $testCase = $problem->getTestCases()[$idx];

            $uploadedInputFilePath = $testCaseDTO->UploadedInputFilePath;
            rename($uploadedInputFilePath, $uploadedInputFilePath . '.tar');
            $uploadedInputFilePath .= '.tar';
    
            $uploadedOutputFilePath = $testCaseDTO->UploadedOutputFilePath;
            rename($uploadedOutputFilePath, $uploadedOutputFilePath . '.tar');
            $uploadedOutputFilePath .= '.tar';

            $this->FileRepository->moveInputFile($uploadedInputFilePath, $testCase);
            $this->FileRepository->moveOutputFile($uploadedOutputFilePath, $testCase);
        }

        $this->ProblemRepository->save($problem);

        return new CreateProblemResponse($problem);
    }
}

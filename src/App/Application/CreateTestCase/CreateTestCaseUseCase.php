<?php

declare(strict_types=1);

namespace App\Application\CreateTestCase;

use App\Domain\File\IFileRepository;
use App\Domain\Problem\Factory\ExecutionRuleFactoryDTO;
use App\Domain\Problem\IProblemRepository;
use RuntimeException;

class CreateTestCaseUseCase
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
     * @param CreateTestCaseRequest $request
     * @return CreateTestCaseResponse
     */
    public function handle(CreateTestCaseRequest $request): CreateTestCaseResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        $executionRules = [];
        foreach ($request->ExecutionRuleDTOs as $ExecutionRuleDTO) {
            $language = $ExecutionRuleDTO->Language;
            $sourceCodeExecutionCommand = $ExecutionRuleDTO->SourceCodeExecutionCommand;
            $sourceCodeCompareCommand = $ExecutionRuleDTO->SourceCodeCompareCommand;
            $fileExecutionCommand = $ExecutionRuleDTO->FileExecutionCommand;
            $fileCompareCommand = $ExecutionRuleDTO->FileCompareCommand;
            $executionRules[] = new ExecutionRuleFactoryDTO($language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
        }
        $problem->createTestCase($request->Title, $executionRules);

        $testCase = current(array_filter($problem->getTestCases(), fn ($e) => $e->getTitle() === $request->Title));
        if ($testCase === false) {
            throw new RuntimeException();
        }

        $uploadedInputFilePath = $request->UploadedInputFilePath;
        rename($uploadedInputFilePath, $uploadedInputFilePath . '.tar');
        $uploadedInputFilePath .= '.tar';

        $uploadedOutputFilePath = $request->UploadedOutputFilePath;
        rename($uploadedOutputFilePath, $uploadedOutputFilePath . '.tar');
        $uploadedOutputFilePath .= '.tar';

        $this->FileRepository->moveInputFile($uploadedInputFilePath, $testCase);
        $this->FileRepository->moveOutputFile($uploadedOutputFilePath, $testCase);

        $this->ProblemRepository->save($problem);

        return new CreateTestCaseResponse($problem);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\CreateTestCase;

use App\Domain\File\IFileRepository;
use App\Domain\Problem\IProblemRepository;

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
        // TODO
    }
}

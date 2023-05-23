<?php

declare(strict_types=1);

namespace App\Application\CreateProblem;

use App\Domain\File\IFileRepository;
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
        // TODO
    }
}

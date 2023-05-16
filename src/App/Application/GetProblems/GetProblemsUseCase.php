<?php

declare(strict_types=1);

namespace App\Application\GetProblems;

use App\Domain\Problem\IProblemRepository;

class GetProblemsUseCase
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
     * @param GetProblemsRequest $request
     * @return GetProblemsResponse
     */
    public function handle(GetProblemsRequest $request): GetProblemsResponse
    {
        return new GetProblemsResponse($this->ProblemRepository->getAll());
    }
}

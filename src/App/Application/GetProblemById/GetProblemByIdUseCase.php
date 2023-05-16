<?php

declare(strict_types=1);

namespace App\Application\GetProblemById;

use App\Domain\Problem\IProblemRepository;

class GetProblemByIdUseCase
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
     * @param GetProblemByIdRequest $request
     * @return GetProblemByIdResponse
     */
    public function handle(GetProblemByIdRequest $request): GetProblemByIdResponse
    {
        return new GetProblemByIdResponse($this->ProblemRepository->findById($request->Id));
    }
}

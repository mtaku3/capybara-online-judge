<?php

declare(strict_types=1);

namespace App\Application\DeleteProblem;

use App\Domain\Problem\IProblemRepository;

class DeleteProblemUseCase
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
     * @param DeleteProblemRequest $request
     * @return DeleteProblemResponse
     */
    public function handle(DeleteProblemRequest $request): DeleteProblemResponse
    {
        $problem = $this->ProblemRepository->findById($request->Id);
        $this->ProblemRepository->delete($problem);

        return new DeleteProblemResponse();
    }
}

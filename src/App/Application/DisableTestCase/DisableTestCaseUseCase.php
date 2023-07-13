<?php

declare(strict_types=1);

namespace App\Application\DisableTestCase;

use App\Domain\Problem\IProblemRepository;

class DisableTestCaseUseCase
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
     * @param DisableTestCaseRequest $request
     * @return DisableTestCaseResponse
     */
    public function handle(DisableTestCaseRequest $request): DisableTestCaseResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);
        $problem->disableTestCase($request->TestCaseId);
        $this->ProblemRepository->save($problem);

        return new DisableTestCaseResponse($problem);
    }
}

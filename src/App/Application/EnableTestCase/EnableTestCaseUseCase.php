<?php

declare(strict_types=1);

namespace App\Application\EnableTestCase;

use App\Domain\Problem\IProblemRepository;

class EnableTestCaseUseCase
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
     * @param EnableTestCaseRequest $request
     * @return EnableTestCaseResponse
     */
    public function handle(EnableTestCaseRequest $request): EnableTestCaseResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);
        $problem->enableTestCase($request->TestCaseId);
        $this->ProblemRepository->save($problem);

        return new EnableTestCaseResponse($problem);
    }
}

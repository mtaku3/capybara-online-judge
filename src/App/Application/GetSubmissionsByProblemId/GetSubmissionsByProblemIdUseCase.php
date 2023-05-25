<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemId;

use App\Domain\Problem\IProblemRepository;
use App\Domain\Submission\ISubmissionRepository;

class GetSubmissionsByProblemIdUseCase
{
    /**
     * @var ISubmissionRepository
     */
    private readonly ISubmissionRepository $SubmissionRepository;
    /**
     * @var IProblemRepository
     */
    private readonly IProblemRepository $ProblemRepository;

    /**
     * @param ISubmissionRepository $submissionRepository
     * @param IProblemRepository $problemRepository
     * @return void
     */
    public function __construct(ISubmissionRepository $submissionRepository, IProblemRepository $problemRepository)
    {
        $this->SubmissionRepository = $submissionRepository;
        $this->ProblemRepository = $problemRepository;
    }

    /**
     * @param GetSubmissionsByProblemIdRequest $request
     * @return GetSubmissionsByProblemIdResponse
     */
    public function handle(GetSubmissionsByProblemIdRequest $request): GetSubmissionsByProblemIdResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        return new GetSubmissionsByProblemIdResponse($this->SubmissionRepository->findByProblemId($problem->getId(), $request->Page, $request->Limit), $this->SubmissionRepository->countByProblemId($problem->getId()));
    }
}

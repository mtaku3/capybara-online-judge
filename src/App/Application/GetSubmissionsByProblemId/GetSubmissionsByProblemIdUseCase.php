<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemId;

use App\Domain\Submission\ISubmissionRepository;

class GetSubmissionsByProblemIdUseCase
{
    /**
     * @var ISubmissionRepository
     */
    private readonly ISubmissionRepository $SubmissionRepository;

    /**
     * @param ISubmissionRepository $submissionRepository
     * @return void
     */
    public function __construct(ISubmissionRepository $submissionRepository)
    {
        $this->SubmissionRepository = $submissionRepository;
    }

    /**
     * @param GetSubmissionsByProblemIdRequest $request
     * @return GetSubmissionsByProblemIdResponse
     */
    public function handle(GetSubmissionsByProblemIdRequest $request): GetSubmissionsByProblemIdResponse
    {
        return new GetSubmissionsByProblemIdResponse($this->SubmissionRepository->findByProblemId($request->ProblemId, $request->Page, $request->Limit), $this->SubmissionRepository->countByProblemId($request->ProblemId));
    }
}

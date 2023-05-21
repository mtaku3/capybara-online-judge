<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemIdAndUserId;

use App\Domain\Submission\ISubmissionRepository;

class GetSubmissionsByProblemIdAndUserIdUseCase
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
     * @param GetSubmissionsByProblemIdAndUserIdRequest $request
     * @return GetSubmissionsByProblemIdAndUserIdResponse
     */
    public function handle(GetSubmissionsByProblemIdAndUserIdRequest $request): GetSubmissionsByProblemIdAndUserIdResponse
    {
        return new GetSubmissionsByProblemIdAndUserIdResponse(
            $this->SubmissionRepository->findByProblemIdAndUserId(
                $request->ProblemId,
                $request->UserId,
                $request->Page,
                $request->Limit
            ),
            $this->SubmissionRepository->countByProblemIdAndUserId(
                $request->ProblemId,
                $request->UserId
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByUserId;

use App\Domain\Submission\ISubmissionRepository;

class GetSubmissionsByUserIdUseCase
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
     * @param GetSubmissionsByUserIdRequest $request
     * @return GetSubmissionsByUserIdResponse
     */
    public function handle(GetSubmissionsByUserIdRequest $request): GetSubmissionsByUserIdResponse
    {
        return new GetSubmissionsByUserIdResponse(
            $this->SubmissionRepository->findByUserId(
                $request->UserId,
                $request->Page,
                $request->Limit
            ),
            $this->SubmissionRepository->countByUserId(
                $request->UserId
            )
        );
    }
}

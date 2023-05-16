<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionById;

use App\Domain\Submission\ISubmissionRepository;

class GetSubmissionByIdUseCase
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
     * @param GetSubmissionByIdRequest $request
     * @return GetSubmissionByIdResponse
     */
    public function handle(GetSubmissionByIdRequest $request): GetSubmissionByIdResponse
    {
        return new GetSubmissionByIdResponse($this->SubmissionRepository->findById($request->Id));
    }
}

<?php

declare(strict_types=1);

namespace App\Application\DeleteSubmission;

use App\Domain\Submission\ISubmissionRepository;

class DeleteSubmissionUseCase
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

    public function handle(DeleteSubmissionRequest $request): DeleteSubmissionResponse
    {
        $submission = $this->SubmissionRepository->findById($request->Id);
        $this->SubmissionRepository->delete($submission);

        return new DeleteSubmissionResponse();
    }
}

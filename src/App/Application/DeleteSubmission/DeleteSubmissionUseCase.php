<?php

declare(strict_types=1);

namespace App\Application\DeleteSubmission;

use App\Domain\File\IFileRepository;
use App\Domain\Submission\ISubmissionRepository;

class DeleteSubmissionUseCase
{
    /**
     * @var ISubmissionRepository
     */
    private readonly ISubmissionRepository $SubmissionRepository;

    /**
     * @var IFileRepository
     */
    private readonly IFileRepository $FileRepository;

    /**
     * @param ISubmissionRepository $submissionRepository
     * @param IFileRepository $fileRepository
     * @return void
     */
    public function __construct(ISubmissionRepository $submissionRepository, IFileRepository $fileRepository)
    {
        $this->SubmissionRepository = $submissionRepository;
        $this->FileRepository = $fileRepository;
    }

    public function handle(DeleteSubmissionRequest $request): DeleteSubmissionResponse
    {
        $submission = $this->SubmissionRepository->findById($request->Id);

        $this->SubmissionRepository->delete($submission);
        $this->FileRepository->deleteSourceFile($submission->getSourceFile());

        return new DeleteSubmissionResponse();
    }
}

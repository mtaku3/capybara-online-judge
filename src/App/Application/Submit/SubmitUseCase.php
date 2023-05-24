<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\File\IFileRepository;
use App\Domain\JudgeQueue\IJudgeQueueRepository;
use App\Domain\Problem\IProblemRepository;
use App\Domain\Submission\ISubmissionRepository;
use App\Domain\User\IUserRepository;

class SubmitUseCase
{
    /**
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;
    /**
     * @var IProblemRepository
     */
    private readonly IProblemRepository $ProblemRepository;
    /**
     * @var IFileRepository
     */
    private readonly IFileRepository $FileRepository;
    /**
     * @var ISubmissionRepository
     */
    private readonly ISubmissionRepository $SubmissionRepository;
    /**
     * @var IJudgeQueueRepository
     */
    private readonly IJudgeQueueRepository $JudgeQueueRepository;

    /**
     * @param IUserRepository $userRepository 
     * @param IProblemRepository $problemRepository 
     * @param IFileRepository $fileRepository 
     * @param ISubmissionRepository $submissionRepository 
     * @param IJudgeQueueRepository $judgeQueueRepository 
     * @return void 
     */
    public function __construct(IUserRepository $userRepository, IProblemRepository $problemRepository, IFileRepository $fileRepository, ISubmissionRepository $submissionRepository, IJudgeQueueRepository $judgeQueueRepository)
    {
        $this->UserRepository = $userRepository;
        $this->ProblemRepository = $problemRepository;
        $this->FileRepository = $fileRepository;
        $this->SubmissionRepository = $submissionRepository;
        $this->JudgeQueueRepository = $judgeQueueRepository;
    }

    /**
     * @param SubmitRequest $request
     * @return SubmitResponse
     */
    public function handle(SubmitRequest $request): SubmitResponse
    {
        // TODO
    }
}

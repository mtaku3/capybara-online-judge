<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemIdAndUserId;

use App\Domain\Problem\IProblemRepository;
use App\Domain\Submission\ISubmissionRepository;
use App\Domain\User\IUserRepository;

class GetSubmissionsByProblemIdAndUserIdUseCase
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
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;

    /**
     * @param ISubmissionRepository $submissionRepository
     * @param IProblemRepository $problemRepository
     * @param IUserRepository $userRepository
     * @return void
     */
    public function __construct(ISubmissionRepository $submissionRepository, IProblemRepository $problemRepository, IUserRepository $userRepository)
    {
        $this->SubmissionRepository = $submissionRepository;
        $this->ProblemRepository = $problemRepository;
        $this->UserRepository = $userRepository;
    }

    /**
     * @param GetSubmissionsByProblemIdAndUserIdRequest $request
     * @return GetSubmissionsByProblemIdAndUserIdResponse
     */
    public function handle(GetSubmissionsByProblemIdAndUserIdRequest $request): GetSubmissionsByProblemIdAndUserIdResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);
        $user = $this->UserRepository->findById($request->UserId);

        return new GetSubmissionsByProblemIdAndUserIdResponse(
            $this->SubmissionRepository->findByProblemIdAndUserId(
                $problem->getId(),
                $user->getId(),
                $request->Page,
                $request->Limit
            ),
            $this->SubmissionRepository->countByProblemIdAndUserId(
                $problem->getId(),
                $user->getId()
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByUserId;

use App\Domain\Submission\ISubmissionRepository;
use App\Domain\User\IUserRepository;

class GetSubmissionsByUserIdUseCase
{
    /**
     * @var ISubmissionRepository
     */
    private readonly ISubmissionRepository $SubmissionRepository;
    /**
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;

    /**
     * @param ISubmissionRepository $submissionRepository
     * @param IUserRepository $userRepository
     * @return void
     */
    public function __construct(ISubmissionRepository $submissionRepository, IUserRepository $userRepository)
    {
        $this->SubmissionRepository = $submissionRepository;
        $this->UserRepository = $userRepository;
    }

    /**
     * @param GetSubmissionsByUserIdRequest $request
     * @return GetSubmissionsByUserIdResponse
     */
    public function handle(GetSubmissionsByUserIdRequest $request): GetSubmissionsByUserIdResponse
    {
        $user = $this->UserRepository->findById($request->UserId);

        return new GetSubmissionsByUserIdResponse(
            $this->SubmissionRepository->findByUserId(
                $user->getId(),
                $request->Page,
                $request->Limit
            ),
            $this->SubmissionRepository->countByUserId(
                $user->getId()
            )
        );
    }
}

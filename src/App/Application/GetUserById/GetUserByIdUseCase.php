<?php

declare(strict_types=1);

namespace App\Application\GetUserById;

use App\Domain\User\IUserRepository;

class GetUserByIdUseCase
{
    /**
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->UserRepository = $userRepository;
    }

    /**
     * @param GetUserByIdRequest $request
     * @return GetUserByIdResponse
     */
    public function handle(GetUserByIdRequest $request): GetUserByIdResponse
    {
        return new GetUserByIdResponse($this->UserRepository->findById($request->UserId));
    }
}

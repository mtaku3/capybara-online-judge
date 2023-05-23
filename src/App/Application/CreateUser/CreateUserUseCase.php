<?php

declare(strict_types=1);

namespace App\Application\CreateUser;

use App\Domain\User\IUserRepository;

class CreateUserUseCase
{
    /**
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;

    /**
     * @param IUserRepository $userRepository 
     * @return void 
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->UserRepository = $userRepository;
    }

    /**
     * @param CreateUserRequest $request
     * @return CreateUserResponse
     */
    public function handle(CreateUserRequest $request): CreateUserResponse
    {
        // TODO
    }
}

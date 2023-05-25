<?php

declare(strict_types=1);

namespace App\Application\CreateUser;

use App\Application\Exception\UsernameAlreadyExistsException;
use App\Domain\User\Entity\User;
use App\Domain\User\IUserRepository;
use App\Infrastructure\Repository\User\Exception\UserNotFoundException;
use App\Presentation\Router\Request;

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
        try {
            $this-> UserRepository->findByUsername($request->Username);
            throw new UsernameAlreadyExistsException();
        } catch (UserNotFoundException $e) {
            // ignored
        }

        $user = User::Create($request->Username, $request->Password, $request->IsAdmin);

        $this-> UserRepository->save($user);

        return new CreateUserResponse($user);
    }
}

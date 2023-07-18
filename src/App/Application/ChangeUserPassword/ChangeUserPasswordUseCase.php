<?php

declare(strict_types=1);

namespace App\Application\ChangeUserPassword;

use App\Application\Exception\WrongPasswordException;
use App\Application\Session\ISessionRepository;
use App\Domain\User\IUserRepository;

class ChangeUserPasswordUseCase
{
    /**
     * @var IUserRepository
     */
    private readonly IUserRepository $UserRepository;

    /**
     * @var ISessionRepository
     */
    private readonly ISessionRepository $SessionRepository;

    public function __construct(IUserRepository $userRepository, ISessionRepository $sessionRepository)
    {
        $this->UserRepository = $userRepository;
        $this->SessionRepository = $sessionRepository;
    }

    /**
     * @param ChangeUserPasswordRequest $request
     * @return ChangeUserPasswordResponse
     */
    public function handle(ChangeUserPasswordRequest $request): ChangeUserPasswordResponse
    {
        $user = $this->UserRepository->findByUsername($request->Username);
        if (!$user->comparePassword($request->CurrentPassword)) {
            throw new WrongPasswordException();
        }

        $user->hashAndSetPassword($request->NewPassword);

        $sessions = $this->SessionRepository->findByUserId($user->getId());

        foreach ($sessions as $session) {
            $this->SessionRepository->delete($session);
        }

        $this->UserRepository->save($user);

        return new ChangeUserPasswordResponse();
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Authorize;

use App\Application\Exception\WrongPasswordException;
use App\Application\Session\Entity\Session;
use App\Application\Session\ISessionRepository;
use App\Application\Session\ValueObject\AccessToken;
use App\Domain\User\IUserRepository;
use Exception;
use DomainException;

class AuthorizeUseCase
{
    /**
     * @var IUserRepository
     */
    public readonly IUserRepository $UserRepository;
    /**
     * @var ISessionRepository
     */
    public readonly ISessionRepository $SessionRepository;

    /**
     * @param IUserRepository $userRepository
     * @param ISessionRepository $sessionRepository
     * @return void
     */
    public function __construct(IUserRepository $userRepository, ISessionRepository $sessionRepository)
    {
        $this->UserRepository = $userRepository;
        $this->SessionRepository = $sessionRepository;
    }

    /**
     * @param AuthorizeRequest $request
     * @return AuthorizeResponse
     * @throws WrongPasswordException
     * @throws Exception
     * @throws DomainException
     */
    public function handle(AuthorizeRequest $request): AuthorizeResponse
    {
        $user = $this->UserRepository->findByUsername($request->Username);

        if (!$user->comparePassword($request->Password)) {
            throw new WrongPasswordException();
        }

        $session = Session::Create($user);
        $this->SessionRepository->save($session);

        $accessToken = AccessToken::Create($user);

        return new AuthorizeResponse($accessToken, $session);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Application\Exception\SessionExpiredException;
use App\Application\Exception\WrongAccessTokenOwner;
use App\Application\Session\ISessionRepository;
use App\Application\Session\ValueObject\AccessToken;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\IUserRepository;
use App\Infrastructure\Repository\Session\Exception\SessionNotFoundException;
use DateTimeImmutable;
use Exception;
use Firebase\JWT\ExpiredException;

class ValidateSessionUseCase
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
     * @param ValidateSessionRequest $request
     * @return ValidateSessionResponse
     */
    public function handle(ValidateSessionRequest $request): ValidateSessionResponse
    {
        $user = $this->UserRepository->findById($request->UserId);

        $accessToken = null;
        if (isset($request->AccessToken)) {
            try {
                $accessToken = new AccessToken($request->AccessToken);
            } catch (ExpiredException $e) {
                // ignored
            }

            if (!$accessToken->getUserId()->equals($request->UserId)) {
                throw new WrongAccessTokenOwner();
            }
        }

        try {
            $session = $this->SessionRepository->findByUserIdAndRefreshToken($user->getId(), new RefreshToken($request->RefreshToken));
        } catch (SessionNotFoundException $e) {
            $session = null;
        }

        if ($session->getExpiresAt() < (new DateTimeImmutable())) {
            $session = null;
        }

        if (isset($accessToken)) {
            return new ValidateSessionResponse($user, $accessToken, $session);
        } elseif (isset($session)) {
            return new ValidateSessionResponse($user, AccessToken::Create($user), $session);
        } else {
            throw new SessionExpiredException();
        }
    }
}

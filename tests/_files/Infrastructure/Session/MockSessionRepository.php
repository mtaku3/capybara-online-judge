<?php

declare(strict_types=1);

namespace Test\Infrastructure\Session;

use App\Application\Session\Entity\Session;
use App\Application\Session\Exception\InvalidAccessTokenException;
use App\Application\Session\ISessionRepository;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\ValueObject\UserId;

class MockSessionRepository implements ISessionRepository
{
    /**
     * @var Session[]
     */
    private array $records = [];

    /**
     * @param UserId $userId
     * @param RefreshToken $refreshToken
     * @return Session
     */
    public function findByUserIdAndRefreshToken(UserId $userId, RefreshToken $refreshToken): Session
    {
        $existingSession = current(array_filter($this->records, fn ($e) => $e->getUserId()->equals($userId) && $e->getRefreshToken()->equals($refreshToken)));

        if ($existingSession !== false) {
            return $existingSession;
        } else {
            throw new InvalidAccessTokenException();
        }
    }

    /**
     * @param UserId $userId
     * @return Session[]
     */
    public function findByUserId(UserId $userId): array
    {
        return array_filter($this->records, fn ($e) => $e->getUserId()->equals($userId));
    }

    /**
     * @param Session $session
     * @return void
     */
    public function save(Session $session): void
    {
        $existingSession = current(array_filter($this->records, fn ($e) => $e->getId()->equals($session->getId())));

        if ($existingSession === false) {
            $this->records[] = $session;
        } else {
            $existingUser = $session;
        }
    }

    /**
     * @param Session $session
     * @return void
     */
    public function delete(Session $session): void
    {
        $this->records = array_filter($this->records, fn ($e) => !($e->getId()->equals($session->getId())));
    }
}

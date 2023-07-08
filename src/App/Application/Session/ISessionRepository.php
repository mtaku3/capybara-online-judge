<?php

declare(strict_types=1);

namespace App\Application\Session;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\ValueObject\UserId;

interface ISessionRepository
{
    /**
     * @param UserId $userId
     * @param RefreshToken $refreshToken
     * @return Session
     */
    public function findByUserAndRefreshToken(UserId $userId, RefreshToken $refreshToken): Session;

    /**
     * @param UserId $userId
     * @return Session[]
     */
    public function findByUserId(UserId $userId): array;

    /**
     * @param Session $session
     * @return void
     */
    public function save(Session $session): void;

    /**
     * @param Session $session
     * @return void
     */
    public function delete(Session $session): void;
}

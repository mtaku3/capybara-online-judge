<?php

declare(strict_types=1);

namespace App\Application\Session;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\Entity\User;

interface ISessionRepository
{
    /**
     * @param User $user
     * @param RefreshToken $refreshToken
     * @return Session
     */
    public function findByUserAndRefreshToken(User $user, RefreshToken $refreshToken): Session;

    /**
     * @param User $user 
     * @return Session[]
     */
    public function findByUser(User $user): array;

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

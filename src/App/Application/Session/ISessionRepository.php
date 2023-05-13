<?php

declare(strict_types=1);

namespace App\Application\Session;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\Entity\User;

interface ISessionRepository
{
    public function findByUserAndRefreshToken(User $user, RefreshToken $refreshToken): Session;
    public function save(Session $session): void;
}

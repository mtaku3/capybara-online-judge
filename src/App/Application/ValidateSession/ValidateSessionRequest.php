<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Application\Session\ValueObject\AccessToken;
use App\Application\Session\ValueObject\RefreshToken;
use App\Domain\User\ValueObject\UserId;

class ValidateSessionRequest
{
    public readonly UserId $UserId;
    public readonly AccessToken $AccessToken;
    public readonly RefreshToken $RefreshToken;

    /**
     * @param UserId $userId
     * @param AccessToken $accessToken
     * @param RefreshToken $refreshToken
     * @return void
     */
    public function __construct(UserId $userId, AccessToken $accessToken, RefreshToken $refreshToken)
    {
        $this->UserId = $userId;
        $this->AccessToken = $accessToken;
        $this->RefreshToken = $refreshToken;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Domain\User\ValueObject\UserId;

class ValidateSessionRequest
{
    /**
     * @var UserId
     */
    public readonly UserId $UserId;
    /**
     * @var string
     */
    public readonly string $AccessToken;
    /**
     * @var string
     */
    public readonly string $RefreshToken;

    /**
     * @param UserId $userId
     * @param string $accessToken
     * @param string $refreshToken
     * @return void
     */
    public function __construct(UserId $userId, string $accessToken, string $refreshToken)
    {
        $this->UserId = $userId;
        $this->AccessToken = $accessToken;
        $this->RefreshToken = $refreshToken;
    }
}

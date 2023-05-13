<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Application\Session\ValueObject\AccessToken;
use App\Application\Session\ValueObject\RefreshToken;
use InvalidArgumentException;

class ValidateSessionResponse
{
    public readonly bool $IsValid;
    public readonly ?AccessToken $AccessToken;
    public readonly ?RefreshToken $RefreshToken;

    /**
     * @param null|AccessToken $accessToken
     * @param null|RefreshToken $refreshToken
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(?AccessToken $accessToken, ?RefreshToken $refreshToken)
    {
        if ((isset($accessToken) && !isset($refreshToken)) || (!isset($accessToken) && isset($refreshToken))) {
            throw new InvalidArgumentException();
        }

        $this->IsValid = isset($accessToken) && isset($refreshToken);
        $this->AccessToken = $accessToken;
        $this->RefreshToken = $refreshToken;
    }
}

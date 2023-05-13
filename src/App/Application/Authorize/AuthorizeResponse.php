<?php

declare(strict_types=1);

namespace App\Application\Authorize;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\AccessToken;

class AuthorizeResponse
{
    /**
     * @var AccessToken
     */
    public readonly AccessToken $AccessToken;
    /**
     * @var Session
     */
    public readonly Session $Session;

    /**
     * @param AccessToken $accessToken
     * @param Session $session
     * @return void
     */
    public function __construct(AccessToken $accessToken, Session $session)
    {
        $this->AccessToken = $accessToken;
        $this->Session = $session;
    }
}

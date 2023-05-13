<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\AccessToken;

class ValidateSessionResponse
{
    /**
     * @var AccessToken
     */
    public readonly AccessToken $AccessToken;
    /**
     * @var null|Session
     */
    public readonly ?Session $Session;

    /**
     * @param AccessToken $accessToken
     * @param null|Session $session
     * @return void
     */
    public function __construct(AccessToken $accessToken, ?Session $session)
    {
        $this->AccessToken = $accessToken;
        $this->Session = $session;
    }
}

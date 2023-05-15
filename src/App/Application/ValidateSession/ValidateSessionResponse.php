<?php

declare(strict_types=1);

namespace App\Application\ValidateSession;

use App\Application\Session\Entity\Session;
use App\Application\Session\ValueObject\AccessToken;
use App\Domain\User\Entity\User;

class ValidateSessionResponse
{
    /**
     * @var User
     */
    public readonly User $User;
    /**
     * @var AccessToken
     */
    public readonly AccessToken $AccessToken;
    /**
     * @var null|Session
     */
    public readonly ?Session $Session;

    /**
     * @param User $user
     * @param AccessToken $accessToken
     * @param null|Session $session
     * @return void
     */
    public function __construct(User $user, AccessToken $accessToken, ?Session $session)
    {
        $this->User = $user;
        $this->AccessToken = $accessToken;
        $this->Session = $session;
    }
}

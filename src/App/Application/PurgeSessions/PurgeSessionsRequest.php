<?php

declare(strict_types=1);

namespace App\Application\PurgeSessions;

use App\Domain\User\Entity\User;

class PurgeSessionsRequest
{
    /**
     * @var User
     */
    public User $User;

    public function __construct(User $user)
    {
        $this->User = $user;
    }
}

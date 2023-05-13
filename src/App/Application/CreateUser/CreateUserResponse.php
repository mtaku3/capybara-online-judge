<?php

declare(strict_types=1);

namespace App\Application\CreateUser;

use App\Domain\User\Entity\User;

class CreateUserResponse
{
    public readonly User $User;

    /**
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->User = $user;
    }
}

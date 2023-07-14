<?php

declare(strict_types=1);

namespace App\Application\GetUserById;

use App\Domain\User\Entity\User;

class GetUserByIdResponse
{
    /**
     * @var User
     */
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

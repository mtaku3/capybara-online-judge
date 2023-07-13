<?php

declare(strict_types=1);

namespace App\Application\GetUserById;

use App\Domain\User\ValueObject\UserId;

class GetUserByIdRequest
{
    /**
     * @var UserId
     */
    public readonly UserId $UserId;

    /**
     * @param App\Domain\User\ValueObject\UserId $userId
     * @return void
     */
    public function __construct(UserId $userId)
    {
        $this->UserId = $userId;
    }
}

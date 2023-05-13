<?php

declare(strict_types=1);

namespace App\Application\Authorize;

use App\Domain\User\ValueObject\UserId;

class AuthorizeRequest
{
    public readonly UserId $UserId;

    /**
     * @param UserId $userId
     * @return void
     */
    public function __construct(UserId $userId)
    {
        $this->UserId = $userId;
    }
}

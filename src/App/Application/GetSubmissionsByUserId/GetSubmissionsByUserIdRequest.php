<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByUserId;

use App\Domain\User\ValueObject\UserId;

class GetSubmissionsByUserIdRequest
{
    /**
     * @var UserId
     */
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

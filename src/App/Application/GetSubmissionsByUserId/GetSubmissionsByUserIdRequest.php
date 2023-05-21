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
     * @var int
     */
    public readonly int $Page;
    /**
     * @var int
     */
    public readonly int $Limit;

    /**
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return void
     */
    public function __construct(UserId $userId, int $page, int $limit)
    {
        $this->UserId = $userId;
        $this->Page = $page;
        $this->Limit = $limit;
    }
}

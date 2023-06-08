<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByUserId;

use App\Domain\User\Entity\User;

class GetSubmissionsByUserIdResponse
{
    /**
     * @var User
     */
    public readonly User $User;
    /**
     * @var \App\Domain\Submission\Entity\Submission[]
     */
    public readonly array $Submissions;
    /**
     * @var int
     */
    public readonly int $Count;

    /**
     * @param User $user
     * @param array $submissions
     * @param int $count
     * @return void
     */
    public function __construct(User $user, array $submissions, int $count)
    {
        $this->User = $user;
        $this->Submissions = $submissions;
        $this->Count = $count;
    }
}

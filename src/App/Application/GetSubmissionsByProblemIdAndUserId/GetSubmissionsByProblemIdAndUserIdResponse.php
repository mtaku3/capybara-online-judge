<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemIdAndUserId;

class GetSubmissionsByProblemIdAndUserIdResponse
{
    /**
     * @var \App\Domain\Submission\Entity\Submission[]
     */
    public readonly array $Submissions;

    /**
     * @param \App\Domain\Submission\Entity\Submission[] $submissions
     * @return void
     */
    public function __construct(array $submissions)
    {
        $this->Submissions = $submissions;
    }
}

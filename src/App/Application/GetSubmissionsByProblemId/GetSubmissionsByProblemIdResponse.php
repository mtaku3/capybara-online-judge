<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionsByProblemId;

class GetSubmissionsByProblemIdResponse
{
    /**
     * @var \App\Domain\Submission\Entity\Submission[]
     */
    public readonly array $Submissions;
    /**
     * @var int
     */
    public readonly int $Count;

    /**
     * @param array $submissions
     * @param int $count
     * @return void
     */
    public function __construct(array $submissions, int $count)
    {
        $this->Submissions = $submissions;
        $this->Count = $count;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\JudgeQueue;

use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;

interface IJudgeQueueRepository
{
    /**
     * @param Submission $submission
     * @return void
     */
    public function enqueue(Submission $submission): void;

    /** @return SubmissionId  */
    public function dequeue(): SubmissionId;
}

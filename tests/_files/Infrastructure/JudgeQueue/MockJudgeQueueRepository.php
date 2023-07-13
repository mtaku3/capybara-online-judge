<?php

declare(strict_types=1);

namespace Test\Infrastructure\JudgeQueue;

use App\Domain\JudgeQueue\IJudgeQueueRepository;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;
use LengthException;

class MockJudgeQueueRepository implements IJudgeQueueRepository
{
    /**
    * @var  SubmissionId[]
    */
    private array $records = [];

    /**
     * @param Submission $submission
     * @return void
     */
    public function enqueue(Submission $submission): void
    {
        array_push($this->records, $submission->getId());
    }

    /**
     * @return SubmissionId
     */
    public function dequeue(): SubmissionId
    {
        if (empty($this->records)) {
            throw new LengthException("Dequeuing from empty queue is not implemented at MockJudgeQueueRepository");
        }

        return array_shift($this->records);
    }
}

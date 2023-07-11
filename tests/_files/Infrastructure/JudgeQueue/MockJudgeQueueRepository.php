<?php

declare(strict_types=1);

namespace Test\Infrastructure\JudgeQueue;

use App\Domain\JudgeQueue\IJudgeQueueRepository;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;

class MockJudgeQueueRepository implements IJudgeQueueRepository
{
    /**
    * @var  SubmissionId[]
    */
    private array $records = [];
    private int   $head=0;
    private int   $tail=0;
    /**
     * @param Submission $submission
     * @return void
     */
    public function enqueue(Submission $submission): void
    {
        $this->records[$this->tail]=$submission->getId();
        $this->tail++;
    }

    /**
     * @return SubmissionId
     */
    public function dequeue(): SubmissionId
    {
        return $this->records[$this->head];
    }
}

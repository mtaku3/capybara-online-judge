<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\JudgeQueue;

use App\Domain\JudgeQueue\IJudgeQueueRepository;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;
use Redis;
use RedisException;

class JudgeQueueRepository implements IJudgeQueueRepository
{
    private const Key = "Judge";

    /**
     * @var Redis
     */
    private readonly Redis $Redis;

    /**
     * @param Redis $redis
     * @return void
     */
    public function __construct(Redis $redis)
    {
        $this->Redis = $redis;
    }

    /**
     * @param Submission $submission
     * @return void
     * @throws RedisException
     */
    public function enqueue(Submission $submission): void
    {
        $this->Redis->rPush(self::Key, (string)$submission->getId());
    }

    /**
     * @return SubmissionId
     * @throws RedisException
     */
    public function dequeue(): SubmissionId
    {
        return new SubmissionId($this->Redis->blPop(self::Key, 0)[1]);
    }

}

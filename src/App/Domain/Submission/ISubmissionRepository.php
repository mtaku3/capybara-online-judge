<?php

declare(strict_types=1);

namespace App\Domain\Submission;

use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;

interface ISubmissionRepository
{
    /**
     * @param SubmissionId $id
     * @return Submission
     */
    public function findById(SubmissionId $id): Submission;

    /**
     * @param UserId $userId
     * @return array<User>
     */
    public function findByUserId(UserId $userId): array;

    /**
     * @param ProblemId $problemId
     * @return array<User>
     */
    public function findByProblemId(ProblemId $problemId): array;

    /**
     * @param Submission $submission
     * @return void
     */
    public function save(Submission $submission): void;
}

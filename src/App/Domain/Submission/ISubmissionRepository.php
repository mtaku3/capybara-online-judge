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
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByUserId(UserId $userId, int $page = 1, int $limit = 10): array;

    /**
     * @param UserId $userId
     * @return int
     */
    public function countByUserId(UserId $userId): int;

    /**
     * @param ProblemId $problemId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByProblemId(ProblemId $problemId, int $page = 1, int $limit = 10): array;

    /**
     * @param ProblemId $problemId
     * @return int
     */
    public function countByProblemId(ProblemId $problemId): int;

    /**
     * @param ProblemId $problemId
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByProblemIdAndUserId(ProblemId $problemId, UserId $userId, int $page = 1, int $limit = 10): array;


    /**
     * @param ProblemId $problemId
     * @param UserId $userId
     * @return int
     */
    public function countByProblemIdAndUserId(ProblemId $problemId, UserId $userId): int;

    /**
     * @param Submission $submission
     * @return void
     */
    public function save(Submission $submission): void;

    /**
     * @param Submission $submission
     * @return void
     */
    public function delete(Submission $submission): void;
}

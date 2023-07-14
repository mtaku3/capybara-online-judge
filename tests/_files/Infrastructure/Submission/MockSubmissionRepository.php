<?php

declare(strict_types=1);

namespace Test\Infrastructure\Submission;

use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ISubmissionRepository;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\Submission\Exception\SubmissionNotFoundException;

class MockSubmissionRepository implements ISubmissionRepository
{
    /**
     * @var Submission[]
     */
    private array $records = [];

    /**
     * @param SubmissionId $id
     * @return Submission
     */
    public function findById(SubmissionId $id): Submission
    {
        $existingSubmission = current(array_filter($this->records, fn ($e) => $e->getId()->equals($id)));

        if ($existingSubmission !== false) {
            return $existingSubmission;
        } else {
            throw new SubmissionNotFoundException();
        }
    }

    /**
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByUserId(UserId $userId, int $page = 1, int $limit = 10): array
    {
        return array_slice(array_filter($this->records, fn ($e) => $e->getUserId()->equals($userId)), ($page - 1) * $limit, $limit);
    }

    /**
     * @param UserId $userId
     * @return int
     */
    public function countByUserId(UserId $userId): int
    {
        return count(array_filter($this->records, fn ($e) => $e->getUserId()->equals($userId)));
    }

    /**
     * @param ProblemId $problemId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByProblemId(ProblemId $problemId, int $page = 1, int $limit = 10): array
    {
        return array_slice(array_filter($this->records, fn ($e) => $e->getProblemId()->equals($problemId)), ($page - 1) * $limit, $limit);
    }

    /**
     * @param ProblemId $problemId
     * @return int
     */
    public function countByProblemId(ProblemId $problemId): int
    {

        return count(array_filter($this->records, fn ($e) => $e->getProblemId()->equals($problemId)));
    }

    /**
     * @param ProblemId $problemId
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     */
    public function findByProblemIdAndUserId(ProblemId $problemId, UserId $userId, int $page = 1, int $limit = 10): array
    {
        return array_slice(array_filter($this->records, fn ($e) => $e->getUserId()->equals($userId) && $e->getProblemId()->equals($problemId)), ($page - 1) * $limit, $limit);
    }

    /**
     * @param ProblemId $problemId
     * @param UserId $userId
     * @return int
     */
    public function countByProblemIdAndUserId(ProblemId $problemId, UserId $userId): int
    {
        return count(array_filter($this->records, fn ($e) => $e->getProblemId()->equals($problemId) && $e->getUserId()->equals($userId)));
    }

    /**
     * @param Submission $submission
     * @return void
     */
    public function save(Submission $submission): void
    {
        $existingSubmission = current(array_filter($this->records, fn ($e) => $e->getId()->equals($submission->getId())));

        if ($existingSubmission === false) {
            $this->records[] = $submission;
        } else {
            $existingSubmission = $submission;
        }
    }
    /**
     * @param Submission $submission
     * @return void
     */
    public function delete(Submission $submission): void
    {
        $this->records = array_filter($this->records, fn ($e) => !($e->getId()->equals($submission->getId())));
    }
}

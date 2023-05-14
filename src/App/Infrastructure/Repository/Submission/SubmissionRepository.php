<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Submission;

use App\Domain\Submission\Entity\Submission;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ISubmissionRepository;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\Submission\Exception\SubmissionNotFoundException;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\RepositoryInterface;

class SubmissionRepository implements ISubmissionRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var RepositoryInterface
     */
    private readonly RepositoryInterface $SubmissionRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RepositoryInterface $submissionRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, RepositoryInterface $submissionRepository)
    {
        $this->EntityManager = $entityManager;
        $this->SubmissionRepository = $submissionRepository;
    }

    /**
     * @param SubmissionId $id
     * @return Submission
     * @throws SubmissionNotFoundException
     */
    public function findById(SubmissionId $id): Submission
    {
        $submission = $this->SubmissionRepository->findByPK($id);

        if (empty($submission)) {
            throw new SubmissionNotFoundException();
        }

        return $submission;
    }

    /**
     * @param UserId $userId
     * @return App\Domain\Submission\User[]
     */
    public function findByUserId(UserId $userId): array
    {
        return (array)$this->SubmissionRepository->findAll([
            "UserId" => $userId
        ]);
    }

    /**
     * @param ProblemId $problemId
     * @return App\Domain\Submission\User[]
     */
    public function findByProblemId(ProblemId $problemId): array
    {
        return (array)$this->SubmissionRepository->findAll([
            "ProblemId" => $problemId
        ]);
    }

    /**
     * @param Submission $submission
     * @return void
     */
    public function save(Submission $submission): void
    {
        $this->EntityManager->persist($submission);
        $this->EntityManager->run();
    }
}

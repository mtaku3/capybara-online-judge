<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Submission;

use App\Domain\Submission\Entity\Submission;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ISubmissionRepository;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\Submission\Exception\SubmissionNotFoundException;
use Cycle\Database\Exception\StatementException;
use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\ParserException;
use Cycle\ORM\Exception\LoaderException;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select\Repository;
use Spiral\Pagination\Paginator;

class SubmissionRepository implements ISubmissionRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var Repository
     */
    private readonly Repository $SubmissionRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Repository $submissionRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, Repository $submissionRepository)
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
     * @param int $page
     * @param int $limit
     * @return Submission[]
     * @throws StatementException
     * @throws ParserException
     * @throws LoaderException
     */
    public function findByUserId(UserId $userId, int $page = 1, int $limit = 10): array
    {
        $select = $this->SubmissionRepository->select()->where("UserId", $userId)->orderBy("SubmittedAt", SelectQuery::SORT_DESC);

        $paginator = new Paginator($limit);
        $paginator->withPage($page)->paginate($select);

        return $select->fetchAll();
    }

    /**
     * @param ProblemId $problemId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     * @throws StatementException
     * @throws ParserException
     * @throws LoaderException
     */
    public function findByProblemId(ProblemId $problemId, int $page = 1, int $limit = 10): array
    {
        $select = $this->SubmissionRepository->select()->where("ProblemId", $problemId)->orderBy("SubmittedAt", SelectQuery::SORT_DESC);

        $paginator = new Paginator($limit);
        $paginator->withPage($page)->paginate($select);

        return $select->fetchAll();
    }

    /**
     * @param ProblemId $problemId
     * @param UserId $userId
     * @param int $page
     * @param int $limit
     * @return Submission[]
     * @throws StatementException
     * @throws ParserException
     * @throws LoaderException
     */
    public function findByProblemIdAndUserId(ProblemId $problemId, UserId $userId, int $page = 1, int $limit = 10): array
    {
        $select = $this->SubmissionRepository->select()->where("ProblemId", $problemId)->andWhere("UserId", $userId)->orderBy("SubmittedAt", SelectQuery::SORT_DESC);

        $paginator = new Paginator($limit);
        $paginator->withPage($page)->paginate($select);

        return $select->fetchAll();
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

    /**
     * @param Submission $submission
     * @return void
     */
    public function delete(Submission $submission): void
    {
        $this->EntityManager->delete($submission);
        $this->EntityManager->run();
    }
}

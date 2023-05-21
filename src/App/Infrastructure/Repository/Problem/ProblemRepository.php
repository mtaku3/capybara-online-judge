<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\IProblemRepository;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;
use Cycle\Database\Exception\StatementException;
use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\BuilderException;
use Cycle\ORM\Exception\ParserException;
use Cycle\ORM\Exception\LoaderException;
use Cycle\ORM\Exception\SchemaException;
use Cycle\ORM\Select\Repository;
use Spiral\Pagination\Paginator;

class ProblemRepository implements IProblemRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var Repository
     */
    private readonly Repository $ProblemRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RepositoryInterface $problemRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, Repository $problemRepository)
    {
        $this->EntityManager = $entityManager;
        $this->ProblemRepository = $problemRepository;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return Problem[]
     * @throws StatementException
     * @throws ParserException
     * @throws LoaderException
     */
    public function fetchAll(int $page = 1, int $limit = 10): array
    {
        $select = $this->ProblemRepository->select()->orderBy("CreatedAt", SelectQuery::SORT_DESC);

        $paginator = new Paginator($limit);
        $paginator->withPage($page)->paginate($select);

        return $select->fetchAll();
    }

    /**
     * @return int
     * @throws SchemaException
     * @throws BuilderException
     */
    public function count(): int
    {
        return $this->ProblemRepository->select()->count();
    }

    /**
     * @param ProblemId $id
     * @return Problem
     * @throws ProblemNotFoundException
     */
    public function findById(ProblemId $id): Problem
    {
        $problem = $this->ProblemRepository->findByPK($id);

        if (empty($problem)) {
            throw new ProblemNotFoundException();
        }

        return $problem;
    }

    /**
     * @param Problem $problem
     * @return void
     */
    public function save(Problem $problem): void
    {
        $this->EntityManager->persist($problem);
        $this->EntityManager->run();
    }

    /**
     * @param Problem $problem
     * @return void
     */
    public function delete(Problem $problem): void
    {
        $this->EntityManager->delete($problem);
        $this->EntityManager->run();
    }
}

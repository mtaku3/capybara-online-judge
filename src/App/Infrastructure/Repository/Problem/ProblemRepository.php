<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\IProblemRepository;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\RepositoryInterface;

class ProblemRepository implements IProblemRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var RepositoryInterface
     */
    private readonly RepositoryInterface $ProblemRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RepositoryInterface $problemRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, RepositoryInterface $problemRepository)
    {
        $this->EntityManager = $entityManager;
        $this->ProblemRepository = $problemRepository;
    }

    /** @return Problem[]  */
    public function getAll(): array
    {
        return (array)$this->ProblemRepository->findAll();
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

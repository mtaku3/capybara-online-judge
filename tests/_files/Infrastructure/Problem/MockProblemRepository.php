<?php

declare(strict_types=1);

namespace Test\Infrastructure\Problem;

use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\IProblemRepository;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;

class MockProblemRepository implements IProblemRepository
{
    /**
     * @var Problem[]
     */
    private array $records = [];

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
        return array_slice($this->records, ($page - 1) * $limit, $limit);
    }

    /**
    * @return int
    * @throws SchemaException
    * @throws BuilderException
    */
    public function count(): int
    {
        return count($this->records);
    }

    /**
     * @param ProblemId $id
     * @return Problem
     * @throws ProblemNotFoundException
     */
    public function findById(ProblemId $id): Problem
    {
        $existingProblem = current(array_filter($this->records, fn ($e) => $e->getId()->equals($id)));

        if ($existingProblem === false) {
            throw new ProblemNotFoundException();
        }

        return $existingProblem;
    }

    /**
    * @param Problem $problem
    * @return void
    */
    public function save(Problem $problem): void
    {
        $existingProblem = current(array_filter($this->records, fn ($e) => $e->getId()->equals($problem->getId())));

        if ($existingProblem === false) {
            $this->records[] = $problem;
        } else {
            $existingProblem = $problem;
        }
    }

    /**
    * @param Problem $problem
    * @return void
    * @throws ProblemNotFoundException
    */
    public function delete(Problem $problem): void
    {
        $this->records = array_filter($this->records, fn ($e) => !($e->equals($problem)));
    }
}

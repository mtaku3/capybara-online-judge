<?php

declare(strict_types=1);

namespace App\Application\UpdateProblemTitleAndBody;

use App\Domain\Problem\IProblemRepository;

class UpdateProblemTitleAndBodyUseCase
{
    /**
     * @var IProblemRepository
     */
    private readonly IProblemRepository $ProblemRepository;

    /**
     * @param IProblemRepository $problemRepository
     * @return void
     */
    public function __construct(IProblemRepository $problemRepository)
    {
        $this->ProblemRepository = $problemRepository;
    }

    /**
     * @param UpdateProblemTitleAndBodyRequest $request
     * @return UpdateProblemTitleAndBodyResponse
     */
    public function handle(UpdateProblemTitleAndBodyRequest $request): UpdateProblemTitleAndBodyResponse
    {
        // TODO
    }
}

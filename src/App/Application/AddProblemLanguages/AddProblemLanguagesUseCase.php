<?php

declare(strict_types=1);

namespace App\Application\AddProblemLanguages;

use App\Domain\Problem\IProblemRepository;

class AddProblemLanguagesUseCase
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
     * @param AddProblemLanguagesRequest $request
     * @return AddProblemLanguagesResponse
     */
    public function handle(AddProblemLanguagesRequest $request): AddProblemLanguagesResponse
    {
        // TODO
    }
}

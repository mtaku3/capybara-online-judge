<?php

declare(strict_types=1);

namespace App\Application\RemoveProblemLanguages;

use App\Domain\Problem\IProblemRepository;

class RemoveProblemLanguagesUseCase
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
     * @param RemoveProblemLanguagesRequest $request
     * @return RemoveProblemLanguagesResponse
     */
    public function handle(RemoveProblemLanguagesRequest $request): RemoveProblemLanguagesResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        foreach ($problem->getCompileRules() as $compileRule) {
            if (array_search($compileRule->getLanguage(), $request->Languages) !== false) {
                $compileRuleID = $compileRule->getId();
                $problem->removeCompileRule($compileRuleID);
            }
        }

        $this->ProblemRepository->save($problem);

        return new RemoveProblemLanguagesResponse($problem);
    }
}

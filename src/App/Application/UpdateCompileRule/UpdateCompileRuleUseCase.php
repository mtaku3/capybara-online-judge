<?php

declare(strict_types=1);

namespace App\Application\UpdateCompileRule;

use App\Domain\Common\Exception\EntityNotFoundException;
use App\Domain\Problem\IProblemRepository;

class UpdateCompileRuleUseCase
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
     * @param UpdateCompileRuleRequest $request
     * @return UpdateCompileRuleResponse
     * @throws EntityNotFoundException
     */
    public function handle(UpdateCompileRuleRequest $request): UpdateCompileRuleResponse
    {
        $problem = $this->ProblemRepository->findById($request->ProblemId);

        $problem->setCompileRuleSourceCodeCompileCommand($request->CompileRuleId, $request->SourceCodeCompileCommand);
        $problem->setCompileRuleFileCompileCommand($request->CompileRuleId, $request->FileCompileCommand);

        $this->ProblemRepository->save($problem);

        return new UpdateCompileRuleResponse();
    }
}

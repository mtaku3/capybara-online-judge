<?php

declare(strict_types=1);

namespace App\Application\CreateTestCase;

use App\Domain\Problem\ValueObject\ProblemId;

class CreateTestCaseRequest
{
    public readonly ProblemId $ProblemId;
    public readonly string $Title;

    /**
     * @param ProblemId $problemId
     * @param string $title
     * @return void
     */
    public function __construct(ProblemId $problemId, string $title)
    {
        $this->ProblemId = $problemId;
        $this->Title = $title;
    }
}

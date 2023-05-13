<?php

declare(strict_types=1);

namespace App\Application\UpdateProblemTitle;

use App\Domain\Problem\ValueObject\ProblemId;

class UpdateProblemTitleRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $ProblemId;
    /**
     * @var string
     */
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

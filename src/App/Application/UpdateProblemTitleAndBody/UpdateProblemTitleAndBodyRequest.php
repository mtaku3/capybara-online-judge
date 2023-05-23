<?php

declare(strict_types=1);

namespace App\Application\UpdateProblemTitleAndBody;

use App\Domain\Problem\ValueObject\ProblemId;

class UpdateProblemTitleAndBodyRequest
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
     * @var string
     */
    public readonly string $Body;

    /**
     * @param ProblemId $problemId
     * @param string $title
     * @param string $body
     * @return void
     */
    public function __construct(ProblemId $problemId, string $title, string $body)
    {
        $this->ProblemId = $problemId;
        $this->Title = $title;
        $this->Body = $body;
    }
}

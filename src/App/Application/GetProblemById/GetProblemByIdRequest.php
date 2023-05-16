<?php

declare(strict_types=1);

namespace App\Application\GetProblemById;

use App\Domain\Problem\ValueObject\ProblemId;

class GetProblemByIdRequest
{
    /**
     * @var ProblemId
     */
    public readonly ProblemId $Id;

    /**
     * @param ProblemId $id
     * @return void
     */
    public function __construct(ProblemId $id)
    {
        $this->Id = $id;
    }
}

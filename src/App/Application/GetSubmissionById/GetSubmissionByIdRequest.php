<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionById;

use App\Domain\Submission\ValueObject\SubmissionId;

class GetSubmissionByIdRequest
{
    /**
     * @var SubmissionId
     */
    public readonly SubmissionId $Id;

    /**
     * @param SubmissionId $id
     * @return void
     */
    public function __construct(SubmissionId $id)
    {
        $this->Id = $id;
    }
}

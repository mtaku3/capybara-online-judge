<?php

declare(strict_types=1);

namespace App\Application\DeleteSubmission;

use App\Domain\Submission\ValueObject\SubmissionId;

class DeleteSubmissionRequest
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

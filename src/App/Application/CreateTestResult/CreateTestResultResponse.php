<?php

declare(strict_types=1);

namespace App\Application\CreateTestResult;

use App\Domain\Submission\Entity\Submission;

class CreateTestResultResponse
{
    public readonly Submission $Submission;

    /**
     * @param Submission $submission
     * @return void
     */
    public function __construct(Submission $submission)
    {
        $this->Submission = $submission;
    }
}

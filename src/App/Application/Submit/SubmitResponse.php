<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Submission\Entity\Submission;

class SubmitResponse
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

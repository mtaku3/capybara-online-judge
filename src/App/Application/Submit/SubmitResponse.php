<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Submission\Entity\Submission;

class SubmitResponse
{
    /**
     * @var Submission
     */
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

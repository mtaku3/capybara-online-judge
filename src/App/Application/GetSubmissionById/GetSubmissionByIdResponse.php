<?php

declare(strict_types=1);

namespace App\Application\GetSubmissionById;

use App\Domain\Submission\Entity\Submission;

class GetSubmissionByIdResponse
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

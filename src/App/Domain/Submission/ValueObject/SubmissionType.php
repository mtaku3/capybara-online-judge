<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

enum SubmissionType: string
{
    case SourceCode = "SourceCode";
    case File = "File";
}

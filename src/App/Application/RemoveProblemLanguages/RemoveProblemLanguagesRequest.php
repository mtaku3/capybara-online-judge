<?php

declare(strict_types=1);

namespace App\Application\RemoveProblemLanguages;

use App\Domain\Problem\ValueObject\ProblemId;

class RemoveProblemLanguagesRequest
{
    public readonly ProblemId $ProblemId;
    /**
     * @var array<Language>
     */
    public readonly array $Languages;

    /**
     * @param ProblemId $problemId
     * @param array<Language> $languages
     * @return void
     */
    public function __construct(ProblemId $problemId, array $languages)
    {
        $this->ProblemId = $problemId;
        $this->Languages = $languages;
    }
}

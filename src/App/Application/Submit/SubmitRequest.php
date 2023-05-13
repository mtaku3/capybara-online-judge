<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Common\ValueObject\Language;

class SubmitRequest
{
    public readonly Language $Language;
    public readonly int $CodeLength;

    /**
     * @param Language $language
     * @param int $codeLength
     * @return void
     */
    public function __construct(Language $language, int $codeLength)
    {
        $this->Language = $language;
        $this->CodeLength = $codeLength;
    }
}

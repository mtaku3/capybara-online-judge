<?php

declare(strict_types=1);

namespace App\Application\Submit;

use App\Domain\Common\ValueObject\Language;

class SubmitRequest
{
    /**
     * @var Language
     */
    public readonly Language $Language;
    /**
     * @var int
     */
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

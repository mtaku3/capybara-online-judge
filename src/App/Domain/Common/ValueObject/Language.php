<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

enum Language: string
{
    case C = "C";
    case CPP = "CPP";

    /**
     * @param Language $target
     * @return int
     */
    public function comparesTo(Language $target): int
    {
        $cases = self::cases();
        return array_search($target, $cases) - array_search($this, $cases);
    }
}

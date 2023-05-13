<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

enum Language: string
{
    case C = "C";
    case CPP = "CPP";
}

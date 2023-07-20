<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

use ValueError;

enum Language: string
{
    case C = "C (GCC 13.1.0)";
    case CPP = "C++ (GCC 13.1.0)";
    case Python = "Python (3.10.12)";
    case PHP = "PHP (8.2.8)";

    /**
     * @param Language $target
     * @return int
     */
    public function comparesTo(Language $target): int
    {
        $cases = self::cases();
        return array_search($target, $cases) - array_search($this, $cases);
    }

    /**
     * @param string $name
     * @return null|static
     */
    public static function tryFromName(string $name): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return static
     * @throws ValueError
     */
    public static function fromName(string $name): static
    {
        $case = self::tryFromName($name);
        if (!$case) {
            throw new ValueError($name.' is not a valid case for enum '.self::class);
        }

        return $case;
    }
}

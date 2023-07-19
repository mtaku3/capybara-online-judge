<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

use ValueError;

enum SubmissionType: string
{
    case SourceCode = "SourceCode";
    case File = "File";

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

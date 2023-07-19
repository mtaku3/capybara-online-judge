<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

use ValueError;

enum SubmissionJudgeResult: string
{
    case AC = "AC";
    case WA = "WA";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";
    case CE = "CE";
    case IE = "IE";
    case WJ = "WJ";

    /**
     * @param TestResultJudgeResult $testResultJudgeResult
     * @return SubmissionJudgeResult
     */
    public static function Cast(TestResultJudgeResult $testResultJudgeResult): SubmissionJudgeResult
    {
        return self::fromName($testResultJudgeResult->name);
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

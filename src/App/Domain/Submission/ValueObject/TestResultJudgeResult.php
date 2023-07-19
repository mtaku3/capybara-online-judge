<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

use ValueError;

enum TestResultJudgeResult: string
{
    case AC = "AC";
    case WA = "WA";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";

    /**
     * @param TestResultJudgeResult $judgeResult
     * @return int
     */
    public function compares(TestResultJudgeResult $judgeResult): int
    {
        return $this->toInt() - $judgeResult->toInt();
    }

    /** @return int  */
    private function toInt(): int
    {
        switch ($this) {
            case TestResultJudgeResult::AC:
                return 0;
                break;
            case TestResultJudgeResult::WA:
                return 1;
                break;
            case TestResultJudgeResult::RE:
                return 2;
                break;
            case TestResultJudgeResult::TLE:
                return 3;
                break;
            case TestResultJudgeResult::MLE:
                return 4;
                break;
        }
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

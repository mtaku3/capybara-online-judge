<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

enum TestResultJudgeResult: string
{
    case AC = "AC";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";
    case WA = "WA";

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
            case TestResultJudgeResult::RE:
                return 1;
                break;
            case TestResultJudgeResult::TLE:
                return 2;
                break;
            case TestResultJudgeResult::MLE:
                return 3;
                break;
            case TestResultJudgeResult::WA:
                return 4;
                break;
        }
    }
}

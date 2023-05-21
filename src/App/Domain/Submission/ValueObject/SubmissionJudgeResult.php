<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

enum SubmissionJudgeResult: string
{
    case AC = "AC";
    case WA = "WA";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";
    case CE = "CE";
    case WJ = "WJ";

    /**
     * @param TestResultJudgeResult $testResultJudgeResult
     * @return SubmissionJudgeResult
     */
    public static function Cast(TestResultJudgeResult $testResultJudgeResult): SubmissionJudgeResult
    {
        return self::from((string)$testResultJudgeResult);
    }
}

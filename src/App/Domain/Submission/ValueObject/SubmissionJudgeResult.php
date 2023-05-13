<?php

declare(strict_types=1);

namespace App\Domain\Submission\ValueObject;

enum SubmissionJudgeResult: string
{
    case AC = "AC";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";
    case CE = "CE";
    case WJ = "WJ";

    public static function cast(TestResultJudgeResult $testResultJudgeResult): SubmissionJudgeResult
    {
        return self::from((string)$testResultJudgeResult);
    }
}
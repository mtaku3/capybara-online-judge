<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObject;

enum JudgeResult: string
{
    case AC = "AC";
    case RE = "RE";
    case TLE = "TLE";
    case MLE = "MLE";
    case CE = "CE";
    case WJ = "WJ";

    public function compares(JudgeResult $judgeResult): int
    {
        return $this->_toInt() - $judgeResult->_toInt();
    }

    private function _toInt(): int
    {
        switch ($this) {
            case JudgeResult::AC:
                return 0;
                break;
            case JudgeResult::RE:
                return 1;
                break;
            case JudgeResult::TLE:
                return 2;
                break;
            case JudgeResult::MLE:
                return 3;
                break;
            case JudgeResult::CE:
                return 4;
                break;
            case JudgeResult::WJ:
                return 5;
                break;
        }
    }
}

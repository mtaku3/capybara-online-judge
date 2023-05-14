<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleORM\TypecastHandler;

use App\Application\Session\ValueObject\SessionId;
use App\Domain\Problem\ValueObject\CompileRuleId;
use App\Domain\Problem\ValueObject\ExecutionRuleId;
use App\Domain\Problem\ValueObject\InputFileId;
use App\Domain\Problem\ValueObject\OutputFileId;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Domain\Submission\ValueObject\SourceFileId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\Submission\ValueObject\TestResultId;
use App\Domain\User\ValueObject\UserId;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;

class UUIDTypecast implements CastableInterface, UncastableInterface
{
    /**
     * @var array
     */
    private array $rules = [];

    /**
     * @param DatabaseInterface $database
     * @return void
     */
    public function __construct(private DatabaseInterface $database)
    {
    }

    /**
     * @param array<string, mixed> $rules
     * @return array<string, mixed>
     */
    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if (array_search($rule, [
                "SessionId",
                "CompileRuleId",
                "ExecutionRuleId",
                "InputFileId",
                "OutputFileId",
                "ProblemId",
                "TestCaseId",
                "SourceFileId",
                "SubmissionId",
                "TestResultId",
                "UserId"
            ]) !== false) {
                unset($rules[$key]);
                $this->rules[$key] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @param array $values
     * @return array
     */
    public function cast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column])) {
                continue;
            }

            switch ($rule) {
                case "SessionId":
                    $values[$column] = new SessionId($values[$column]);
                    break;
                case "CompileRuleId":
                    $values[$column] = new CompileRuleId($values[$column]);
                    break;
                case "ExecutionRuleId":
                    $values[$column] = new ExecutionRuleId($values[$column]);
                    break;
                case "InputFileId":
                    $values[$column] = new InputFileId($values[$column]);
                    break;
                case "OutputFileId":
                    $values[$column] = new OutputFileId($values[$column]);
                    break;
                case "ProblemId":
                    $values[$column] = new ProblemId($values[$column]);
                    break;
                case "TestCaseId":
                    $values[$column] = new TestCaseId($values[$column]);
                    break;
                case "SourceFileId":
                    $values[$column] = new SourceFileId($values[$column]);
                    break;
                case "SubmissionId":
                    $values[$column] = new SubmissionId($values[$column]);
                    break;
                case "TestResultId":
                    $values[$column] = new TestResultId($values[$column]);
                    break;
                case "UserId":
                    $values[$column] = new UserId($values[$column]);
                    break;
            }
        }

        return $values;
    }

    /**
     * @param array $values
     * @return array
     */
    public function uncast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column])) {
                continue;
            }

            $values[$column] = (string)$values[$column];
        }

        return $values;
    }
}

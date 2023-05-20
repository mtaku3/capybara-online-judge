<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleORM;

use App\Application\Session\Entity\Session;
use App\Domain\Problem\Entity\CompileRule;
use App\Domain\Problem\Entity\ExecutionRule;
use App\Domain\Problem\Entity\InputFile;
use App\Domain\Problem\Entity\OutputFile;
use App\Domain\Problem\Entity\Problem;
use App\Domain\Problem\Entity\TestCase;
use App\Domain\Submission\Entity\SourceFile;
use App\Domain\Submission\Entity\Submission;
use App\Domain\Submission\Entity\TestResult;
use App\Domain\User\Entity\User;
use App\Infrastructure\CycleORM\TypecastHandler\LanguageTypecast;
use App\Infrastructure\CycleORM\TypecastHandler\RefreshTokenTypecast;
use App\Infrastructure\CycleORM\TypecastHandler\SubmissionJudgeResultTypecast;
use App\Infrastructure\CycleORM\TypecastHandler\SubmissionTypeTypeCast;
use App\Infrastructure\CycleORM\TypecastHandler\TestResultJudgeResultTypecast;
use App\Infrastructure\CycleORM\TypecastHandler\UUIDTypecast;
use Cycle\ORM\Parser\Typecast;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;

$Schema = new Schema([
    User::class => [
        Schema::TABLE => "Users",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "Username",
            "Password",
            "IsAdmin",
        ],
        Schema::TYPECAST => [
            "Id" => "UserId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    InputFile::class => [
        Schema::TABLE => "Files",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "Path"
        ],
        Schema::TYPECAST => [
            "Id" => "InputFileId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    OutputFile::class => [
        Schema::TABLE => "Files",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "Path"
        ],
        Schema::TYPECAST => [
            "Id" => "OutputFileId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    SourceFile::class => [
        Schema::TABLE => "Files",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "Path"
        ],
        Schema::TYPECAST => [
            "Id" => "SourceFileId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    Session::class => [
        Schema::TABLE => "Sessions",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "UserId",
            "RefreshToken",
            "ExpiresAt"
        ],
        Schema::TYPECAST => [
            "Id" => "SessionId",
            "UserId" => "UserId",
            "RefreshToken" => "RefreshToken",
            "ExpiresAt" => "datetime"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class,
            RefreshTokenTypecast::class
        ]
    ],
    Problem::class => [
        Schema::TABLE => "Problems",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "Title",
            "Body",
            "TimeConstraint",
            "MemoryConstraint"
        ],
        Schema::RELATIONS => [
            "CompileRules" => [
                Relation::TYPE => Relation::HAS_MANY,
                Relation::TARGET => CompileRule::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "Id",
                    Relation::OUTER_KEY => "ProblemId"
                ]
            ],
            "TestCases" => [
                Relation::TYPE => Relation::HAS_MANY,
                Relation::TARGET => TestCase::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "Id",
                    Relation::OUTER_KEY => "ProblemId"
                ]
            ]
        ],
        Schema::TYPECAST => [
            "Id" => "ProblemId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    CompileRule::class => [
        Schema::TABLE => "CompileRules",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "ProblemId",
            "Language",
            "SourceCodeCompileCommand",
            "FileCompileCommand"
        ],
        Schema::TYPECAST => [
            "Id" => "CompileRuleId",
            "ProblemId" => "ProblemId",
            "Language" => "Language"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class,
            LanguageTypecast::class
        ]
    ],
    TestCase::class => [
        Schema::TABLE => "TestCases",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "ProblemId",
            "Title",
            "IsDisabled",
            "InputFileId",
            "OutputFileId"
        ],
        Schema::RELATIONS => [
            "ExecutionRules" => [
                Relation::TYPE => Relation::HAS_MANY,
                Relation::TARGET => ExecutionRule::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "Id",
                    Relation::OUTER_KEY => "TestCaseId"
                ]
            ],
            "InputFile" => [
                Relation::TYPE => Relation::BELONGS_TO,
                Relation::TARGET => InputFile::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "InputFileId",
                    Relation::OUTER_KEY => "Id"
                ]
            ],
            "OutputFile" => [
                Relation::TYPE => Relation::BELONGS_TO,
                Relation::TARGET => OutputFile::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "OutputFileId",
                    Relation::OUTER_KEY => "Id"
                ]
            ]
        ],
        Schema::TYPECAST => [
            "Id" => "TestCaseId",
            "ProblemId" => "ProblemId",
            "InputFileId" => "InputFileId",
            "OutputFileId" => "OutputFileId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class
        ]
    ],
    ExecutionRule::class => [
        Schema::TABLE => "ExecutionRules",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "TestCaseId",
            "Language",
            "SourceCodeExecutionCommand",
            "SourceCodeCompareCommand",
            "FileExecutionCommand",
            "FileCompareCommand"
        ],
        Schema::TYPECAST => [
            "Id" => "ExecutionRuleId",
            "TestCaseId" => "TestCaseId",
            "Language" => "Language"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class,
            LanguageTypecast::class
        ]
    ],
    Submission::class => [
        Schema::TABLE => "Submissions",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "UserId",
            "ProblemId",
            "SubmittedAt",
            "Language",
            "CodeLength",
            "JudgeResult",
            "ExecutionTime",
            "ConsumedMemory",
            "SubmissionType",
            "SourceFileId"
        ],
        Schema::RELATIONS => [
            "TestResults" => [
                Relation::TYPE => Relation::HAS_MANY,
                Relation::TARGET => TestResult::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "Id",
                    Relation::OUTER_KEY => "SubmissionId"
                ]
            ],
            "SourceFile" => [
                Relation::TYPE => Relation::BELONGS_TO,
                Relation::TARGET => SourceFile::class,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::INNER_KEY => "SourceFileId",
                    Relation::OUTER_KEY => "Id"
                ]
            ]
        ],
        Schema::TYPECAST => [
            "Id" => "SubmissionId",
            "UserId" => "UserId",
            "ProblemId" => "ProblemId",
            "SubmittedAt" => "datetime",
            "Language" => "Language",
            "JudgeResult" => "SubmissionJudgeResult",
            "SubmissionType" => "SubmissionType",
            "SourceFileId" => "SourceFileId"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class,
            LanguageTypecast::class,
            SubmissionJudgeResultTypecast::class,
            SubmissionTypeTypeCast::class
        ]
    ],
    TestResult::class => [
        Schema::TABLE => "TestResults",
        Schema::PRIMARY_KEY => "Id",
        Schema::COLUMNS => [
            "Id",
            "SubmissionId",
            "TestCaseId",
            "JudgeResult",
            "ExecutionTime",
            "ConsumedMemory"
        ],
        Schema::TYPECAST => [
            "Id" => "TestResultId",
            "SubmissionId" => "SubmissionId",
            "TestCaseId" => "TestCaseId",
            "JudgeResult" => "TestResultJudgeResult"
        ],
        Schema::TYPECAST_HANDLER => [
            Typecast::class,
            UUIDTypecast::class,
            TestResultJudgeResultTypecast::class
        ]
    ]
]);

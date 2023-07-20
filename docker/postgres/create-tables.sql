CREATE TABLE
    "Users" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "Username" varchar(20) NOT NULL UNIQUE,
        "Password" text NOT NULL,
        "IsAdmin" bool NOT NULL
    );

CREATE TABLE
    "Files" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "Path" text NOT NULL UNIQUE
    );

CREATE TABLE
    "Sessions" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "UserId" varchar(36) NOT NULL,
        "RefreshToken" text NOT NULL,
        "ExpiresAt" timestamp NOT NULL,
        FOREIGN KEY("UserId") REFERENCES "Users"("Id") ON DELETE CASCADE
    );

CREATE TABLE
    "Problems" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "Title" text NOT NULL,
        "Body" text NOT NULL,
        "TimeConstraint" int NOT NULL,
        "MemoryConstraint" int NOT NULL,
        "CreatedAt" timestamp NOT NULL
    );

CREATE TYPE
    Language AS ENUM('C', 'CPP', 'Python', 'PHP', 'Java');

CREATE TABLE
    "CompileRules" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "ProblemId" varchar(36) NOT NULL,
        "Language" Language NOT NULL,
        "SourceCodeCompileCommand" text NOT NULL,
        "FileCompileCommand" text NOT NULL,
        FOREIGN KEY("ProblemId") REFERENCES "Problems"("Id") ON DELETE CASCADE
    );

CREATE TABLE
    "TestCases" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "ProblemId" varchar(36) NOT NULL,
        "Title" text NOT NULL,
        "IsDisabled" bool NOT NULL,
        "InputFileId" varchar(36) NOT NULL UNIQUE,
        "OutputFileId" varchar(36) NOT NULL UNIQUE,
        UNIQUE ("ProblemId", "Title"),
        FOREIGN KEY("ProblemId") REFERENCES "Problems"("Id") ON DELETE CASCADE,
        FOREIGN KEY("InputFileId") REFERENCES "Files"("Id"),
        FOREIGN KEY("OutputFileId") REFERENCES "Files"("Id")
    );

CREATE TABLE
    "ExecutionRules" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "TestCaseId" varchar(36) NOT NULL,
        "Language" Language NOT NULL,
        "SourceCodeExecutionCommand" text NOT NULL,
        "SourceCodeCompareCommand" text NOT NULL,
        "FileExecutionCommand" text NOT NULL,
        "FileCompareCommand" text NOT NULL,
        FOREIGN KEY("TestCaseId") REFERENCES "TestCases"("Id") ON DELETE CASCADE
    );

CREATE TYPE
    SubmissionJudgeResult AS ENUM('AC', 'WA', 'RE', 'TLE', 'MLE', 'CE', 'IE', 'WJ');

CREATE TYPE
    SubmissionType AS ENUM('SourceCode', 'File');

CREATE TABLE
    "Submissions" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "UserId" varchar(36) NOT NULL,
        "ProblemId" varchar(36) NOT NULL,
        "SubmittedAt" timestamp NOT NULL,
        "Language" Language NOT NULL,
        "CodeLength" int NOT NULL,
        "JudgeResult" SubmissionJudgeResult NOT NULL,
        "ExecutionTime" int,
        "ConsumedMemory" int,
        "SubmissionType" SubmissionType NOT NULL,
        "SourceFileId" varchar(36) NOT NULL UNIQUE,
        FOREIGN KEY("UserId") REFERENCES "Users"("Id") ON DELETE CASCADE,
        FOREIGN KEY("ProblemId") REFERENCES "Problems"("Id") ON DELETE CASCADE,
        FOREIGN KEY("SourceFileId") REFERENCES "Files"("Id")
    );

CREATE TYPE
    TestResultJudgeResult AS ENUM('AC', 'WA', 'RE', 'TLE', 'MLE');

CREATE TABLE
    "TestResults" (
        "Id" varchar(36) NOT NULL PRIMARY KEY,
        "SubmissionId" varchar(36) NOT NULL,
        "TestCaseId" varchar(36) NOT NULL,
        "JudgeResult" TestResultJudgeResult NOT NULL,
        "ExecutionTime" int NOT NULL,
        "ConsumedMemory" int NOT NULL,
        FOREIGN KEY("SubmissionId") REFERENCES "Submissions"("Id") ON DELETE CASCADE,
        FOREIGN KEY("TestCaseId") REFERENCES "TestCases"("Id") ON DELETE CASCADE
    );

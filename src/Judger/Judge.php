<?php

declare(strict_types=1);

use App\Domain\Problem\Entity\Problem;
use App\Domain\Submission\ValueObject\SubmissionType;
use Judger\DockerEngineAPI\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../Bootstrap.php';

$container = $GLOBALS["container"];

/** @var \Cycle\ORM\ORM */
$orm = $container->get("ORM");
/** @var \App\Domain\JudgeQueue\IJudgeQueueRepository */
$judgeQueueRepository = $container->get("JudgeQueueRepository");
/** @var \App\Domain\Submission\ISubmissionRepository */
$submissionRepository = $container->get("SubmissionRepository");
/** @var \App\Domain\Problem\IProblemRepository */
$problemRepository = $container->get("ProblemRepository");

$client = new Client();

$logger = new Logger("Judger");
$logger->pushHandler(new StreamHandler("php://stdout", Level::Debug));

$logger->info("Waiting for new task");

while (1) {
    $submissionId = $judgeQueueRepository->dequeue();

    $logger->info("-- Task received: {$submissionId} --");

    try {
        $submission = $submissionRepository->findById($submissionId);
        $problem = $problemRepository->findById($submission->getProblemId());

        $containerId = null;

        try {
            // Create a container to judge
            $res = $client->createContainer([
                "Image" => strtolower("COJ-" . $submission->getLanguage()->name),
                "Tty" => true,
                "HostConfig" => [
                    "Memory" => (int)(Problem::MaxMemoryConstraint * 1.5) * 1024,
                    "MemorySwap" => (int)(Problem::MaxMemoryConstraint * 1.5) * 1024,
                    "NetworkMode" => "none",
                    "Runtime" => "runsc"
                ]
            ]);
            $containerId = $res["Id"];
            $logger->info("Container created: {$containerId}");

            // Start the container
            $logger->info("Starting container");
            $client->startContainer($containerId);

            // Copy the source files submitted by the user to the container
            $logger->info("Extracting source files to the container");
            $client->extractArchiveInContainer(
                $containerId,
                "/workspace",
                fopen($submission->getSourceFile()->getPath(), "r")
            );

            // Find a compileRule matches to the submitted language from the problem
            $compileRule = current(array_filter($problem->getCompileRules(), fn ($e) => $e->getLanguage() === $submission->getLanguage()));

            if ($compileRule === false) {
                throw new Exception("No compileRule found for the language {$submission->getLanguage()->name} in the problem {$problem->getId()}. The language might have been disabled after the submission.");
            }

            /** Compile the program */
            // 1. Create a exec instance to compile
            $commandToCompile = $submission->getSubmissionType() === SubmissionType::SourceCode ? $compileRule->getSourceCodeCompileCommand() : $compileRule->getFileCompileCommand();
            $res = $client->createExecInstance($containerId, [
                "AttachStdout" => true,
                "Cmd" => explode(" ", $commandToCompile),
                "WorkingDir" => "/workspace"
            ]);
            $execId = $res["Id"];

            // 2. Start the exec instance
            $logger->info("Compiling the program");
            $client->startExecInstance($execId);

            // 3. Retrieve the result
            $res = $client->inspectExecInstance($execId);

            if ($res["ExitCode"] === 0) {
                // Compiling succeeded

                $enabledTestCases = array_filter($problem->getTestCases(), fn ($e) => !$e->getIsDisabled());
                // Run the testCases
                foreach ($enabledTestCases as $testCase) {
                    $logger->info("(*) Running the testCase {$testCase->getId()}");

                    // Find a executionRule matches to the submitted language from the testCase
                    $executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getLanguage() === $submission->getLanguage()));
                    if ($executionRule === false) {
                        throw new Exception("No executionRule found for the language {$submission->getLanguage()->name} in the testCase {$testCase->getId()}. The language might have been disabled after the submission.");
                    }

                    // Copy the inputFile to the container
                    $client->extractArchiveInContainer(
                        $containerId,
                        "/workspace",
                        fopen($testCase->getInputFile()->getPath(), "r")
                    );

                    // Copy the outputFile to the container
                    $client->extractArchiveInContainer(
                        $containerId,
                        "/workspace",
                        fopen($testCase->getOutputFile()->getPath(), "r")
                    );

                    /** Execute the program */
                    // 1. Create a exec instance to execute
                    $commandToExecute = $submission->getSubmissionType() === SubmissionType::SourceCode ? $executionRule->getSourceCodeExecutionCommand() : $executionRule->getFileExecutionCommand();
                    $timeoutDuration = ($problem->getTimeConstraint() * 1.5) / 1000;
                    $res = $client->createExecInstance($containerId, [
                        "AttachStdout" => true,
                        "Cmd" => ["/bin/sh", "-c", "/usr/bin/time -q -f %e,%M,%x -o /tmp/stats /usr/bin/timeout -s SIGKILL --preserve-status {$timeoutDuration} /bin/sh -c \"{$commandToExecute}\""],
                        "WorkingDir" => "/workspace"
                    ]);
                    $execId = $res["Id"];

                    // 2. Start the exec instance
                    $logger->info("Executing the program");
                    $client->startExecInstance($execId);

                    /** Retrieve the stats */
                    // 1. Create a exec instance to retrieve the stats
                    $res = $client->createExecInstance($containerId, [
                        "AttachStdout" => true,
                        "Cmd" => ["/bin/sh", "-c", "/bin/cat /tmp/stats"],
                        "WorkingDir" => "/workspace"
                    ]);
                    $execId = $res["Id"];

                    // 2. Start the exec instance
                    $res = $client->startExecInstance($execId);
                    $stdout = $res->Stdout;

                    // 3. Summarize the stats
                    $statsObj = explode(",", $stdout);
                    $stats = [
                        "ExecutionTime" => (int)(floatval($statsObj[0]) * 1000),
                        "ConsumedMemory" => intval($statsObj[1]),
                        "ExitCode" => intval($statsObj[2])
                    ];

                    /** Compare the output from the program and the expected output */
                    // 1. Create a exec instance to compare
                    $commandToCompare = $submission->getSubmissionType() === SubmissionType::SourceCode ? $executionRule->getSourceCodeCompareCommand() : $executionRule->getFileCompareCommand();
                    $res = $client->createExecInstance($containerId, [
                        "AttachStdout" => true,
                        "Cmd" => ["/bin/sh", "-c", $commandToCompare],
                        "WorkingDir" => "/workspace"
                    ]);
                    $execId = $res["Id"];

                    // 2. Start the exec instance
                    $logger->info("Comparing the output");
                    $client->startExecInstance($execId);

                    // 3. Retrieve the result
                    $res = $client->inspectExecInstance($execId);
                    $compareExitCode = $res["ExitCode"];

                    $submission->createTestResult(
                        $problem,
                        $testCase->getId(),
                        $compareExitCode !== 0,
                        $stats["ExitCode"] !== 0,
                        $stats["ExecutionTime"],
                        $stats["ConsumedMemory"]
                    );
                    $testResultJudgeResult = current(array_filter($submission->getTestResults(), fn ($e) => $e->getTestCaseId()->equals($testCase->getId())))->getJudgeResult()->name;
                    $logger->info("TestCase has been resulted in {$testResultJudgeResult}");
                    $logger->info("(*) TestCase {$testCase->getId()} has been completed");
                }
            }

            // Complete the judge
            $submission->completeJudge();
        } catch (Throwable $e) {
            $logger->error("An error occurred while judging.", [
                "exception" => $e
            ]);

            // Complete the judge with the internal error
            $submission->completeJudge(hasInternalErrorOccured: true);
        } finally {
            if (isset($containerId)) {
                // Stop the container
                $client->stopContainer($containerId);

                // Remove the container
                // $client->removeContainer($containerId);
            }
        }

        // Save the submission to the database
        $submissionRepository->save($submission);

        $orm->getHeap()->clean();
    } catch (Throwable $e) {
        $logger->error("An error occurred while the database interaction.", [
            "exception" => $e
        ]);
    }

    $logger->info("-- Task has been completed. --");
}

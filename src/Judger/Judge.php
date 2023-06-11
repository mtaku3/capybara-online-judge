<?php

declare(strict_types=1);

use App\Domain\Problem\Entity\Problem;
use App\Domain\Submission\ValueObject\SubmissionType;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../Bootstrap.php';

$container = $GLOBALS["container"];

/** @var \App\Domain\JudgeQueue\IJudgeQueueRepository */
$judgeQueueRepository = $container->get("JudgeQueueRepository");
/** @var \App\Domain\Submission\ISubmissionRepository */
$submissionRepository = $container->get("SubmissionRepository");
/** @var \App\Domain\Problem\IProblemRepository */
$problemRepository = $container->get("ProblemRepository");

$client = new Client([
    "base_uri" => "http:/v1.42/",
    "curl" => [
        CURLOPT_UNIX_SOCKET_PATH => "/var/run/docker.sock"
    ],
    "http_errors" => false
]);

while (1) {
    $submissionId = $judgeQueueRepository->dequeue();

    $submission = $submissionRepository->findById($submissionId);
    $problem = $problemRepository->findById($submission->getProblemId());

    try {
        try {
            $res = $client->post("containers/create", [
                RequestOptions::JSON => [
                    "Image" => strtolower("COJ-" . $submission->getLanguage()->name),
                    "Tty" => true,
                    "HostConfig" => [
                        "Memory" => (int)(Problem::MaxMemoryConstraint * 1.5) * 1024,
                        "MemorySwap" => (int)(Problem::MaxMemoryConstraint * 1.5) * 1024,
                        "NetworkMode" => "none"
                    ]
                ]
            ]);

            if ($res->getStatusCode() !== 201) {
                throw new Exception("Failed to create container:" . (string)$res->getBody());
            }

            $body = json_decode((string)$res->getBody(), true);
            $containerId = $body["Id"];

            $res = $client->post("containers/" . $containerId . "/start");

            if ($res->getStatusCode() !== 204) {
                throw new Exception("Failed to start container:" . (string)$res->getBody());
            }

            $res = $client->put("containers/" . $containerId . "/archive", [
                RequestOptions::QUERY => [
                    "path" => "/workspace"
                ],
                RequestOptions::HEADERS => [
                    "Content-Type" => "application/x-tar"
                ],
                RequestOptions::BODY => fopen($submission->getSourceFile()->getPath(), "r")
            ]);

            if ($res->getStatusCode() !== 200) {
                throw new Exception("Failed to extract source files in the container: " . (string)$res->getBody());
            }

            $compileRule = current(array_filter($problem->getCompileRules(), fn ($e) => $e->getLanguage() === $submission->getLanguage()));

            if ($compileRule === false) {
                throw new Exception("CompileRule for Language::" . $submission->getLanguage()->name . ", Problem(" . $problem->getId() . ") has not been found.");
            }

            $res = $client->post("containers/" . $containerId . "/exec", [
                RequestOptions::JSON => [
                    "AttachStdout" => true,
                    "Cmd" => explode(" ", $submission->getSubmissionType() === SubmissionType::SourceCode ? $compileRule->getSourceCodeCompileCommand() : $compileRule->getFileCompileCommand()),
                    "WorkingDir" => "/workspace"
                ]
            ]);

            if ($res->getStatusCode() !== 201) {
                throw new Exception("Failed to create an exec instance: " . (string)$res->getBody());
            }

            $body = json_decode((string)$res->getBody(), true);
            $execId = $body["Id"];

            $res = $client->post("exec/" . $execId . "/start");

            if ($res->getStatusCode() !== 200) {
                throw new Exception("Failed to start an exec instance: " . (string)$res->getBody());
            }

            $res = $client->get("exec/" . $execId . "/json");

            if ($res->getStatusCode() !== 200) {
                throw new Exception("Failed to inspect an exec instance: " . (string)$res->getBody());
            }

            $body = json_decode((string)$res->getBody(), true);

            if ($body["ExitCode"] === 0) {
                foreach ($problem->getTestCases() as $testCase) {
                    $executionRule = current(array_filter($testCase->getExecutionRules(), fn ($e) => $e->getLanguage() === $submission->getLanguage()));

                    if ($executionRule === false) {
                        throw new Exception("ExecutionRule for Language::" . $submission->getLanguage()->name . ", TestCase(" . $testCase->getId() . ") has not been found.");
                    }

                    $res = $client->put("containers/" . $containerId . "/archive", [
                        RequestOptions::QUERY => [
                            "path" => "/workspace"
                        ],
                        RequestOptions::HEADERS => [
                            "Content-Type" => "application/x-tar"
                        ],
                        RequestOptions::BODY => fopen($testCase->getInputFile()->getPath(), "r")
                    ]);

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to extract input files in the container: " . (string)$res->getBody());
                    }

                    $res = $client->put("containers/" . $containerId . "/archive", [
                        RequestOptions::QUERY => [
                            "path" => "/workspace"
                        ],
                        RequestOptions::HEADERS => [
                            "Content-Type" => "application/x-tar"
                        ],
                        RequestOptions::BODY => fopen($testCase->getOutputFile()->getPath(), "r")
                    ]);

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to extract output files in the container: " . (string)$res->getBody());
                    }

                    $res = $client->post("containers/" . $containerId . "/exec", [
                        RequestOptions::JSON => [
                            "AttachStdout" => true,
                            "Cmd" => ["sh", "-c", "/usr/bin/time -f %e,%M,%x -o /tmp/stats " . ($submission->getSubmissionType() === SubmissionType::SourceCode ? $executionRule->getSourceCodeExecutionCommand() : $executionRule->getFileExecutionCommand())],
                            "WorkingDir" => "/workspace"
                        ]
                    ]);

                    if ($res->getStatusCode() !== 201) {
                        throw new Exception("Failed to create an exec instance: " . (string)$res->getBody());
                    }

                    $body = json_decode((string)$res->getBody(), true);
                    $execId = $body["Id"];

                    $res = $client->post("exec/" . $execId . "/start");

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to start an exec instance: " . (string)$res->getBody());
                    }

                    $res = $client->get("exec/" . $execId . "/json");

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to inspect an exec instance: " . (string)$res->getBody());
                    }

                    $res = $client->post("containers/" . $containerId . "/exec", [
                        RequestOptions::JSON => [
                            "AttachStdout" => true,
                            "Cmd" => ["cat", "/tmp/stats"],
                            "WorkingDir" => "/workspace"
                        ]
                    ]);

                    if ($res->getStatusCode() !== 201) {
                        throw new Exception("Failed to create an exec instance: " . (string)$res->getBody());
                    }

                    $body = json_decode((string)$res->getBody(), true);
                    $execId = $body["Id"];

                    $res = $client->post("exec/" . $execId . "/start");

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to start an exec instance: " . (string)$res->getBody());
                    }

                    $statsStr = explode(",", (string)$res->getBody());
                    $stats = [
                        "ExecutionTime" => (int)(floatval($statsStr[0]) * 1000),
                        "ConsumedMemory" => intval($statsStr[1]),
                        "ExitCode" => intval($statsStr[2])
                    ];

                    $res = $client->post("containers/" . $containerId . "/exec", [
                        RequestOptions::JSON => [
                            "AttachStdout" => true,
                            "Cmd" => ["sh", "-c", $submission->getSubmissionType() === SubmissionType::SourceCode ? $executionRule->getSourceCodeCompareCommand() : $executionRule->getFileCompareCommand()],
                            "WorkingDir" => "/workspace"
                        ]
                    ]);

                    if ($res->getStatusCode() !== 201) {
                        throw new Exception("Failed to create an exec instance: " . (string)$res->getBody());
                    }

                    $body = json_decode((string)$res->getBody(), true);
                    $execId = $body["Id"];

                    $res = $client->post("exec/" . $execId . "/start");

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to start an exec instance: " . (string)$res->getBody());
                    }

                    $res = $client->get("exec/" . $execId . "/json");

                    if ($res->getStatusCode() !== 200) {
                        throw new Exception("Failed to inspect an exec instance: " . (string)$res->getBody());
                    }

                    $body = json_decode((string)$res->getBody(), true);

                    $submission->createTestResult($problem, $testCase->getId(), $body["ExitCode"] !== 0, $stats["ExitCode"] !== 0, $stats["ExecutionTime"], $stats["ConsumedMemory"]);
                }
            }

            $submission->completeJudge();
            $submissionRepository->save($submission);
        } finally {
            $res = $client->post("containers/" . $containerId . "/stop");

            if ($res->getStatusCode() !== 204) {
                throw new Exception("Failed to stop container:" . (string)$res->getBody());
            }

            $res = $client->delete("containers/" . $containerId);

            if ($res->getStatusCode() !== 204) {
                throw new Exception("Failed to remove container:" . (string)$res->getBody());
            }
        }
    } catch (Throwable $e) {
        echo $e;
    }
}

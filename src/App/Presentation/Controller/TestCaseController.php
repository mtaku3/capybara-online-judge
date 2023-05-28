<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\AddProblemLanguages\AddProblemLanguagesRequest;
use App\Application\AddProblemLanguages\AddProblemLanguagesUseCase;
use App\Application\CreateTestCase\CreateTestCaseRequest;
use App\Application\CreateTestCase\CreateTestCaseUseCase;
use App\Application\DisableTestCase\DisableTestCaseRequest;
use App\Application\DisableTestCase\DisableTestCaseUseCase;
use App\Application\EnableTestCase\EnableTestCaseRequest;
use App\Application\EnableTestCase\EnableTestCaseUseCase;
use App\Application\GetProblemById\GetProblemByIdRequest;
use App\Application\GetProblemById\GetProblemByIdUseCase;
use App\Application\RemoveProblemLanguages\RemoveProblemLanguagesRequest;
use App\Application\RemoveProblemLanguages\RemoveProblemLanguagesUseCase;
use App\Application\UpdateTestCase\UpdateTestCaseRequest;
use App\Application\UpdateTestCase\UpdateTestCaseUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ExecutionRuleId;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Problem\ValueObject\TestCaseId;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use Twig\Environment;

class TestCaseController
{
    /**
     * @var Environment
     */
    private readonly Environment $Twig;

    /**
     * @var GetProblemByIdUseCase
     */
    private readonly GetProblemByIdUseCase $GetProblemByIdUseCase;

    /**
     * @var AddProblemLanguagesUseCase
     */
    private readonly AddProblemLanguagesUseCase $AddProblemLanguagesUseCase;

    /**
     * @var RemoveProblemLanguagesUseCase
     */
    private readonly RemoveProblemLanguagesUseCase $RemoveProblemLanguagesUseCase;

    /**
     * @var UpdateTestCaseUseCase
     */
    private readonly UpdateTestCaseUseCase $UpdateTestCaseUseCase;

    /**
     * @var CreateTestCaseUseCase
     */
    private readonly CreateTestCaseUseCase $CreateTestCaseUseCase;

    /**
     * @var EnableTestCaseUseCase
     */
    private readonly EnableTestCaseUseCase $EnableTestCaseUseCase;

    /**
     * @var DisableTestCaseUseCase
     */
    private readonly DisableTestCaseUseCase $DisableTestCaseUseCase;

    /**
     * @param Environment $twig
     * @param GetProblemByIdUseCase $getProblemByIdUseCase
     * @param AddProblemLanguagesUseCase $addProblemLanguagesUseCase
     * @param RemoveProblemLanguagesUseCase $removeProblemLanguagesUseCase
     * @param UpdateTestCaseUseCase $updateTestCaseUseCase
     * @param CreateTestCaseUseCase $createTestCaseUseCase
     * @param EnableTestCaseUseCase $enableTestCaseUseCase
     * @param DisableTestCaseUseCase $disableTestCaseUseCase
     * @return void
     */
    public function __construct(Environment $twig, GetProblemByIdUseCase $getProblemByIdUseCase, AddProblemLanguagesUseCase $addProblemLanguagesUseCase, RemoveProblemLanguagesUseCase $removeProblemLanguagesUseCase, UpdateTestCaseUseCase $updateTestCaseUseCase, CreateTestCaseUseCase $createTestCaseUseCase, EnableTestCaseUseCase $enableTestCaseUseCase, DisableTestCaseUseCase $disableTestCaseUseCase)
    {
        $this->Twig = $twig;
        $this->GetProblemByIdUseCase = $getProblemByIdUseCase;
        $this->AddProblemLanguagesUseCase = $addProblemLanguagesUseCase;
        $this->RemoveProblemLanguagesUseCase = $removeProblemLanguagesUseCase;
        $this->UpdateTestCaseUseCase = $updateTestCaseUseCase;
        $this->CreateTestCaseUseCase = $createTestCaseUseCase;
        $this->EnableTestCaseUseCase = $enableTestCaseUseCase;
        $this->DisableTestCaseUseCase = $disableTestCaseUseCase;
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function get(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest(new ProblemId($req->problemId)));

            $res->body(
                $this->Twig->render("/TestCases.twig", [
                    "testCases" => $getProblemByIdResponse->Problem->getTestCases()
                ])
            )->send();
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleLanguageChanges(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);

            $compileRuleDTOs = [];
            foreach ($req->compileRules as $compileRule) {
                $language = Language::from($compileRule["language"]);
                $sourceCodeCompileCommand = $compileRule["sourceCodeCompileCommand"];
                $fileCompileCommand = $compileRule["fileCompileCommand"];
                $compileRuleDTOs[] = new \App\Application\AddProblemLanguages\DTO\CompileRuleDTO($language, $sourceCodeCompileCommand, $fileCompileCommand);
            }

            $executionRuleDTOs = [];
            foreach ($req->executionRules as $executionRule) {
                $language = Language::from($executionRule["language"]);
                $sourceCodeExecutionCommand = $executionRule["sourceCodeExecutionCommand"];
                $sourceCodeCompareCommand = $executionRule["sourceCodeCompareCommand"];
                $fileExecutionCommand = $executionRule["fileExecutionCommand"];
                $fileCompareCommand = $executionRule["fileCompareCommand"];
                $executionRuleDTOs[] = new \App\Application\AddProblemLanguages\DTO\ExecutionRuleDTO($language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
            }

            $this->AddProblemLanguagesUseCase->handle(new AddProblemLanguagesRequest($problemId, $compileRuleDTOs, $executionRuleDTOs));

            $languagesToRemove = array_map(fn ($e) => Language::from($e), $req->languagesToRemove);

            $this->RemoveProblemLanguagesUseCase->handle(new RemoveProblemLanguagesRequest($problemId, $languagesToRemove));

            $res->redirect("/problem/" . $problemId . "/testcases");
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleUpdate(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);
            $testCaseId = new TestCaseId($req->testCaseId);

            $executionRuleDTOs = [];
            foreach ($req->executionRules as $executionRule) {
                $executionRuleId = new ExecutionRuleId($executionRule["executionRuleId"]);
                $sourceCodeExecutionCommand = $executionRule["sourceCodeExecutionCommand"];
                $sourceCodeCompareCommand = $executionRule["sourceCodeCompareCommand"];
                $fileExecutionCommand = $executionRule["fileExecutionCommand"];
                $fileCompareCommand = $executionRule["fileCompareCommand"];
                $executionRuleDTOs[] = new \App\Application\UpdateTestCase\DTO\ExecutionRuleDTO($executionRuleId, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
            }

            $this->UpdateTestCaseUseCase->handle(new UpdateTestCaseRequest($problemId, $testCaseId, $executionRuleDTOs));

            $res->redirect("/problem/" . $problemId . "/testcases");
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

        /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleCreate(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($use) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);
            $title = $req->title;
            $uploadedInputFilePath = $req->files()->inputFile["tmp_name"];
            $uploadedOutputFilePath = $req->files()->outputFile["tmp_name"];

            $executionRuleDTOs = [];
            foreach ($req->executionRules as $executionRule) {
                $language = Language::from($executionRule["language"]);
                $sourceCodeExecutionCommand = $executionRule["sourceCodeExecutionCommand"];
                $sourceCodeCompareCommand = $executionRule["sourceCodeCompareCommand"];
                $fileExecutionCommand = $executionRule["fileExecutionCommand"];
                $fileCompareCommand = $executionRule["fileCompareCommand"];

                $executionRuleDTOs[] = new \App\Application\CreateTestCase\DTO\ExecutionRuleDTO($language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
            }

            $createTestCaseResponse = $this->CreateTestCaseUseCase->handle(new CreateTestCaseRequest($problemId, $title, $executionRuleDTOs, $uploadedInputFilePath, $uploadedOutputFilePath));
            $problem = $createTestCaseResponse->Problem;

            $res->redirect("/problem/" . $problem->getId() . "/testcases");
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleEnable(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($use) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);
            $testCaseId = new TestCaseId($req->testCaseId);

            $this->EnableTestCaseUseCase->handle(new EnableTestCaseRequest($problemId, $testCaseId));

            $res->redirect("/problem/" . $problemId . "/testcases");
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

        /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleDisable(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($use) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);
            $testCaseId = new TestCaseId($req->testCaseId);

            $this->DisableTestCaseUseCase->handle(new DisableTestCaseRequest($problemId, $testCaseId));

            $res->redirect("/problem/" . $problemId . "/testcases");
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleDownloadInputFile(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);

            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest($problemId));
            $problem = $getProblemByIdResponse->Problem;

            $testCase = current(array_filter($problem->getTestCases(), fn ($e) => $e->getId()->equals($problemId)));
            if ($testCase === false) {
                $res->code(404)->send();
                return;
            }

            $inputFilePath = $testCase->getInputFile()->getPath();

            $res->header("Content-Description", "File Transfer");
            $res->header("Content-Type", "application/octet-stream");
            $res->header("Content-Disposition", "attachment; filename=\"" . basename($inputFilePath) . "\"");
            $res->header("Expires", 0);
            $res->header("Cache-Control", "must-revalidate");
            $res->header("Pragma", "public");
            $res->header("Content-Length", filesize($inputFilePath));
            $res->body(readfile($inputFilePath));
            $res->send();
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }

    /**
     * @param Request $req
     * @param AbstractResponse $res
     * @return void
     * @throws ResponseAlreadySentException
     */
    public function handleDownloadOutputFile(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $problemId = new ProblemId($req->problemId);

            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest($problemId));
            $problem = $getProblemByIdResponse->Problem;

            $testCase = current(array_filter($problem->getTestCases(), fn ($e) => $e->getId()->equals($problemId)));
            if ($testCase === false) {
                $res->code(404)->send();
                return;
            }

            $outputFilePath = $testCase->getOutputFile()->getPath();

            $res->header("Content-Description", "File Transfer");
            $res->header("Content-Type", "application/octet-stream");
            $res->header("Content-Disposition", "attachment; filename=\"" . basename($outputFilePath) . "\"");
            $res->header("Expires", 0);
            $res->header("Cache-Control", "must-revalidate");
            $res->header("Pragma", "public");
            $res->header("Content-Length", filesize($outputFilePath));
            $res->body(readfile($outputFilePath));
            $res->send();
        } catch (ProblemNotFoundException $e) {
            $res->code(404)->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }
}

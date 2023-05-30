<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\CreateProblem\CreateProblemRequest;
use App\Application\CreateProblem\CreateProblemUseCase;
use App\Application\DeleteProblem\DeleteProblemRequest;
use App\Application\DeleteProblem\DeleteProblemUseCase;
use App\Application\GetProblemById\GetProblemByIdRequest;
use App\Application\GetProblemById\GetProblemByIdUseCase;
use App\Application\Submit\SubmitRequest;
use App\Application\Submit\SubmitUseCase;
use App\Application\UpdateProblemTitleAndBody\UpdateProblemTitleAndBodyRequest;
use App\Application\UpdateProblemTitleAndBody\UpdateProblemTitleAndBodyUseCase;
use App\Domain\Common\ValueObject\Language;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionType;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;
use App\Presentation\Router\Exceptions\HttpException;
use App\Presentation\Router\Response;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class ProblemController
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
     * @var SubmitUseCase
     */
    private readonly SubmitUseCase $SubmitUseCase;

    /**
     * @var CreateProblemUseCase
     */
    private readonly CreateProblemUseCase $CreateProblemUseCase;

    /**
     * @var UpdateProblemTitleAndBodyUseCase
     */
    private readonly UpdateProblemTitleAndBodyUseCase $UpdateProblemTitleAndBodyUseCase;

    /**
     * @var DeleteProblemUseCase
     */
    private readonly DeleteProblemUseCase $DeleteProblemUseCase;

    /**
     * @param Environment $twig
     * @param GetProblemByIdUseCase $getProblemByIdUseCase
     * @param SubmitUseCase $submitUseCase
     * @param CreateProblemUseCase $createProblemUseCase
     * @param UpdateProblemTitleAndBodyUseCase $updateProblemTitleAndBodyUseCase
     * @param DeleteProblemUseCase $deleteProblemUseCase
     * @return void
     */
    public function __construct(Environment $twig, GetProblemByIdUseCase $getProblemByIdUseCase, SubmitUseCase $submitUseCase, CreateProblemUseCase $createProblemUseCase, UpdateProblemTitleAndBodyUseCase $updateProblemTitleAndBodyUseCase, DeleteProblemUseCase $deleteProblemUseCase)
    {
        $this->Twig = $twig;
        $this->GetProblemByIdUseCase = $getProblemByIdUseCase;
        $this->SubmitUseCase = $submitUseCase;
        $this->CreateProblemUseCase = $createProblemUseCase;
        $this->UpdateProblemTitleAndBodyUseCase = $updateProblemTitleAndBodyUseCase;
        $this->DeleteProblemUseCase = $deleteProblemUseCase;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws LoaderError
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     */
    public function get(Request $req, Response $res)
    {
        try {
            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest(new ProblemId($req->problemId)));
            $problem = $getProblemByIdResponse->Problem;
            $submittableLanguages = array_filter($problem->getCompileRules(), fn ($e) => $e->getLanguage());
            $res->body(
                $this->Twig->render("Problem.twig", [
                    "problemId" => new ProblemId($req->problemId),
                    "problemTitle" => $problem->getTitle(),
                    "problemTimeConstraint" => $problem->getTimeConstraint(),
                    "problemMemoryConstraint" => $problem->getMemoryConstraint(),
                    "problemBody" => $problem->getBody(),
                    "problemSubmittableLanguages" => $submittableLanguages
                ])
            )->send();
        } catch (ProblemNotFoundException) {
            throw HttpException::createFromCode(404);
        }
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws Exception
     * @throws LockedResponseException
     */
    public function handleCreate(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user) || !$user->getIsAdmin()) {
            throw HttpException::createFromCode(401);
        }

        $title = $req->title;
        $body = $req->body;
        $timeConstraint = intval($req->timeConstraint);
        $memoryConstraint = intval($req->memoryConstraint);

        $compileRuleDTOs = [];
        foreach ($req->compileRules as $compileRule) {
            $language = Language::from($compileRule["language"]);
            $sourceCodeCompileCommand = $compileRule["sourceCodeCompileCommand"];
            $fileCompileCommand = $compileRule["fileCompileCommand"];

            $compileRuleDTOs[] = new \App\Application\CreateProblem\DTO\CompileRuleDTO($language, $sourceCodeCompileCommand, $fileCompileCommand);
        }

        $testCaseDTOs = [];
        $inputFiles = $req->files()->inputFiles;
        $outputFiles = $req->files()->ouputFiles;
        foreach ($req->testCases as $idx => $testCase) {
            $testCaseTitle = $testCase["title"];

            $executionRuleDTOs = [];
            foreach ($testCase["executionRules"] as $executionRule) {
                $language = Language::from($executionRule["language"]);
                $sourceCodeExecutionCommand = $executionRule["sourceCodeExecutionCommand"];
                $sourceCodeCompareCommand = $executionRule["sourceCodeCompareCommand"];
                $fileExecutionCommand = $executionRule["fileExecutionCommand"];
                $fileCompareCommand = $executionRule["fileCompareCommand"];

                $executionRuleDTOs[] = new \App\Application\CreateProblem\DTO\ExecutionRuleDTO($language, $sourceCodeExecutionCommand, $sourceCodeCompareCommand, $fileExecutionCommand, $fileCompareCommand);
            }

            if ($inputFiles["error"][$idx] !== UPLOAD_ERR_OK) {
                throw new Exception("Something went wrong while uploading a file");
            }
            if ($outputFiles["error"][$idx] !== UPLOAD_ERR_OK) {
                throw new Exception("Something went wrong while uploading a file");
            }

            $uploadedInputFilePath = $inputFiles["tmp_name"][$idx];
            $uploadedOutputFilePath = $outputFiles["tmp_name"][$idx];

            $testCasesDTOs[] = new \App\Application\CreateProblem\DTO\TestCaseDTO($testCaseTitle, $executionRuleDTOs, $uploadedInputFilePath, $uploadedOutputFilePath);
        }

        $createProblemResponse = $this->CreateProblemUseCase->handle(new CreateProblemRequest($title, $body, $timeConstraint, $memoryConstraint, $compileRuleDTOs, $testCaseDTOs));
        $problem = $createProblemResponse->Problem;

        $res->redirect("/problem/" . $problem->getId());
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws LockedResponseException
     */
    public function handleUpdate(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user) || !$user->getIsAdmin()) {
            throw HttpException::createFromCode(401);
        }

        $title = $req->title;
        $body = $req->body;

        $updateProblemTitleAndBodyResponse = $this->UpdateProblemTitleAndBodyUseCase->handle(new UpdateProblemTitleAndBodyRequest(new ProblemId($req->problemId), $title, $body));
        $problem = $updateProblemTitleAndBodyResponse->Problem;

        $res->redirect("/problem/" . $problem->getId());
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws LockedResponseException
     */
    public function handleDelete(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user) || !$user->getIsAdmin()) {
            throw HttpException::createFromCode(401);
        }

        $this->DeleteProblemUseCase->handle(new DeleteProblemRequest(new ProblemId($req->problemId)));

        $res->redirect("/");
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws Exception
     * @throws LockedResponseException
     */
    public function handleSubmit(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user)) {
            throw HttpException::createFromCode(401);
        }

        $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest(new ProblemId($req->problemId)));
        $problem = $getProblemByIdResponse->Problem;

        $language = Language::from($req->language);
        $submissionType = SubmissionType::from($req->submissionType);

        if ($submissionType === SubmissionType::SourceCode) {
            $tmpf = tmpfile();
            fwrite($tmpf, $req->sourceCode);

            $this->SubmitUseCase->handle(new SubmitRequest($user->getId(), $problem->getId(), $language, $submissionType, stream_get_meta_data($tmpf)["uri"]));

            fclose($tmpf);
        } else {
            if ($req->files()->sourceFile["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("Something went wrong while uploading a files");
            }

            $this->SubmitUseCase->handle(new SubmitRequest($user->getId(), $problem->getId(), $language, $submissionType, $req->files()->sourceFile["tmp_name"]));
        }

        $res->redirect("/problem/" . $problem->getId());
    }
}

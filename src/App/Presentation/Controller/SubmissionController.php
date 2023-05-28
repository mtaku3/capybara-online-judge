<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DeleteSubmission\DeleteSubmissionRequest;
use App\Application\DeleteSubmission\DeleteSubmissionUseCase;
use App\Application\GetProblemById\GetProblemByIdRequest;
use App\Application\GetProblemById\GetProblemByIdUseCase;
use App\Application\GetSubmissionById\GetSubmissionByIdRequest;
use App\Application\GetSubmissionById\GetSubmissionByIdUseCase;
use App\Application\GetSubmissionsByProblemId\GetSubmissionsByProblemIdRequest;
use App\Application\GetSubmissionsByProblemId\GetSubmissionsByProblemIdUseCase;
use App\Application\GetSubmissionsByProblemIdAndUserId\GetSubmissionsByProblemIdAndUserIdRequest;
use App\Application\GetSubmissionsByProblemIdAndUserId\GetSubmissionsByProblemIdAndUserIdUseCase;
use App\Application\GetSubmissionsByUserId\GetSubmissionsByUserIdRequest;
use App\Application\GetSubmissionsByUserId\GetSubmissionsByUserIdUseCase;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\Submission\Exception\SubmissionNotFoundException;
use App\Presentation\Router\AbstractResponse;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use Twig\Environment;

class SubmissionController
{
    private const LimitPerPage = 10;

    /**
     * @var Environment
     */
    private readonly Environment $Twig;

    /**
     * @var GetSubmissionsByProblemIdUseCase
     */
    private readonly GetSubmissionsByProblemIdUseCase $GetSubmissionsByProblemIdUseCase;

    /**
     * @var GetSubmissionsByProblemIdAndUserIdUseCase
     */
    private readonly GetSubmissionsByProblemIdAndUserIdUseCase $GetSubmissionsByProblemIdAndUserIdUseCase;

    /**
     * @var GetSubmissionsByUserIdUseCase
     */
    private readonly GetSubmissionsByUserIdUseCase $GetSubmissionsByUserIdUseCase;

    /**
     * @var GetSubmissionByIdUseCase
     */
    private readonly GetSubmissionByIdUseCase $GetSubmissionByIdUseCase;

    /**
     * @var GetProblemByIdUseCase
     */
    private readonly GetProblemByIdUseCase $GetProblemByIdUseCase;

    /**
     * @var DeleteSubmissionUseCase
     */
    private readonly DeleteSubmissionUseCase $DeleteSubmissionUseCase;

    /**
     * @param Environment $twig
     * @param GetSubmissionsByProblemIdUseCase $getSubmissionsByProblemIdUseCase
     * @param GetSubmissionsByProblemIdAndUserIdUseCase $getSubmissionsByProblemIdAndUserIdUseCase
     * @param GetSubmissionsByUserIdUseCase $getSubmissionsByUserIdUseCase
     * @param GetSubmissionByIdUseCase $getSubmissionByIdUseCase
     * @param GetProblemByIdUseCase $getProblemByIdUseCase
     * @param DeleteSubmissionUseCase $deleteSubmissionUseCase
     * @return void
     */
    public function __construct(Environment $twig, GetSubmissionsByProblemIdUseCase $getSubmissionsByProblemIdUseCase, GetSubmissionsByProblemIdAndUserIdUseCase $getSubmissionsByProblemIdAndUserIdUseCase, GetSubmissionsByUserIdUseCase $getSubmissionsByUserIdUseCase, GetSubmissionByIdUseCase $getSubmissionByIdUseCase, GetProblemByIdUseCase $getProblemByIdUseCase, DeleteSubmissionUseCase $deleteSubmissionUseCase)
    {
        $this->Twig = $twig;
        $this->GetSubmissionsByProblemIdUseCase = $getSubmissionsByProblemIdUseCase;
        $this->GetSubmissionsByProblemIdAndUserIdUseCase = $getSubmissionsByProblemIdAndUserIdUseCase;
        $this->GetSubmissionsByUserIdUseCase = $getSubmissionsByUserIdUseCase;
        $this->GetSubmissionByIdUseCase = $getSubmissionByIdUseCase;
        $this->GetProblemByIdUseCase = $getProblemByIdUseCase;
        $this->DeleteSubmissionUseCase = $deleteSubmissionUseCase;
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
            if (!isset($user)) {
                $res->code(401)->send();
                return;
            }

            $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
            $submission = $getSubmissionByIdResponse->Submission;

            if (!$user->getIsAdmin() && !$submission->getUserId()->equals($user->getId())) {
                $res->code(401)->send();
                return;
            }

            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest($submission->getProblemId()));
            $problem = $getProblemByIdResponse->Problem;

            $res->body(
                $this->Twig->render("Submission.twig", [
                    "submission" => $submission,
                    "problem" => $problem
                ])
            )->send();
        } catch (SubmissionNotFoundException $e) {
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
    public function getByProblem(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user)) {
                $res->code(401)->send();
                return;
            }

            if ($user->getIsAdmin()) {
                $getSubmissionsByProblemIdResponse = $this->GetSubmissionsByProblemIdUseCase->handle(new GetSubmissionsByProblemIdRequest(new ProblemId($req->problemId), intval($req->param("page", 1)), self::LimitPerPage));
                $submissions = $getSubmissionsByProblemIdResponse->Submissions;
                $count = $getSubmissionsByProblemIdResponse->Count;
            } else {
                $getSubmissionsByProblemIdAndUserIdResponse = $this->GetSubmissionsByProblemIdAndUserIdUseCase->handle(new GetSubmissionsByProblemIdAndUserIdRequest(new ProblemId($req->problemId), $user->getId(), intval($req->param("page", 1)), self::LimitPerPage));
                $submissions = $getSubmissionsByProblemIdAndUserIdResponse->Submissions;
                $count = $getSubmissionsByProblemIdAndUserIdResponse->Count;
            }

            $res->body(
                $this->Twig->render("Problem/Submissions.twig", [
                    "submissions" => $submissions,
                    "page" => intval($req->param("page", 1)),
                    "totalNumberOfSubmissions" => $count,
                    "totalNumberOfPages" => intval(ceil($count / self::LimitPerPage)),
                    "limitPerPage" => self::LimitPerPage
                ])
            )->send();
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
    public function getByUser(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user)) {
                $res->code(401)->send();
                return;
            }

            $requestedUserId = new UserId($req->userId);

            if (!$user->getIsAdmin() && !$user->getId()->equals($requestedUserId)) {
                $res->code(401)->send();
                return;
            }

            $getSubmissionsByUserIdResponse = $this->GetSubmissionsByUserIdUseCase->handle(new GetSubmissionsByUserIdRequest($requestedUserId, intval($req->param("page", 1)), self::LimitPerPage));
            $count = $getSubmissionsByUserIdResponse->Count;

            $res->body(
                $this->Twig->render("User/Submissions.twig", [
                    "requestedUser" => $getSubmissionsByUserIdResponse->User,
                    "submissions" => $getSubmissionsByUserIdResponse->Submissions,
                    "page" => intval($req->param("page", 1)),
                    "totalNumberOfSubmissions" => $count,
                    "totalNumberOfPages" => intval(ceil($count / self::LimitPerPage)),
                    "limitPerPage" => self::LimitPerPage
                ])
            )->send();
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
    public function handleDelete(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
            $submission = $getSubmissionByIdResponse->Submission;

            $this->DeleteSubmissionUseCase->handle(new DeleteSubmissionRequest($submission->getId()));

            $res->redirect("/problem/" . $submission->getProblemId());
        } catch (SubmissionNotFoundException $e) {
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
    public function handleDownload(Request $req, AbstractResponse $res)
    {
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                $res->code(401)->send();
                return;
            }

            $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
            $submission = $getSubmissionByIdResponse->Submission;
            $sourceFilePath = $submission->getSourceFile()->getPath();

            $res->header("Content-Description", "File Transfer");
            $res->header("Content-Type", "application/octet-stream");
            $res->header("Content-Disposition", "attachment; filename=\"" . basename($sourceFilePath) . "\"");
            $res->header("Expires", 0);
            $res->header("Cache-Control", "must-revalidate");
            $res->header("Pragma", "public");
            $res->header("Content-Length", filesize($sourceFilePath));
            $res->body(readfile($sourceFilePath));
            $res->send();
        } catch (Exception $e) {
            $res->code(400)->send();
        }
    }
}

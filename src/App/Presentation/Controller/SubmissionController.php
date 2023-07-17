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
use App\Application\GetUserById\GetUserByIdRequest;
use App\Application\GetUserById\GetUserByIdUseCase;
use App\Domain\Problem\ValueObject\ProblemId;
use App\Domain\Submission\ValueObject\SubmissionId;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\Problem\Exception\ProblemNotFoundException;
use App\Infrastructure\Repository\Submission\Exception\SubmissionNotFoundException;
use App\Presentation\Router\Exceptions\HttpException;
use App\Presentation\Router\Exceptions\LockedResponseException;
use App\Presentation\Router\Response;
use App\Presentation\Router\Exceptions\ResponseAlreadySentException;
use App\Presentation\Router\Request;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

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
     * @var GetUserByIdUseCase
     */
    private readonly GetUserByIdUseCase $GetUserByIdUseCase;

    /**
     * @param Environment $twig
     * @param GetSubmissionsByProblemIdUseCase $getSubmissionsByProblemIdUseCase
     * @param GetSubmissionsByProblemIdAndUserIdUseCase $getSubmissionsByProblemIdAndUserIdUseCase
     * @param GetSubmissionsByUserIdUseCase $getSubmissionsByUserIdUseCase
     * @param GetSubmissionByIdUseCase $getSubmissionByIdUseCase
     * @param GetProblemByIdUseCase $getProblemByIdUseCase
     * @param DeleteSubmissionUseCase $deleteSubmissionUseCase
     * @param GetUserByIdUseCase $getUserByIdUseCase
     * @return void
     */
    public function __construct(Environment $twig, GetSubmissionsByProblemIdUseCase $getSubmissionsByProblemIdUseCase, GetSubmissionsByProblemIdAndUserIdUseCase $getSubmissionsByProblemIdAndUserIdUseCase, GetSubmissionsByUserIdUseCase $getSubmissionsByUserIdUseCase, GetSubmissionByIdUseCase $getSubmissionByIdUseCase, GetProblemByIdUseCase $getProblemByIdUseCase, DeleteSubmissionUseCase $deleteSubmissionUseCase, GetUserByIdUseCase $getUserByIdUseCase)
    {
        $this->Twig = $twig;
        $this->GetSubmissionsByProblemIdUseCase = $getSubmissionsByProblemIdUseCase;
        $this->GetSubmissionsByProblemIdAndUserIdUseCase = $getSubmissionsByProblemIdAndUserIdUseCase;
        $this->GetSubmissionsByUserIdUseCase = $getSubmissionsByUserIdUseCase;
        $this->GetSubmissionByIdUseCase = $getSubmissionByIdUseCase;
        $this->GetProblemByIdUseCase = $getProblemByIdUseCase;
        $this->DeleteSubmissionUseCase = $deleteSubmissionUseCase;
        $this->GetUserByIdUseCase = $getUserByIdUseCase;
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
            $user = $req->user;
            if (!isset($user)) {
                throw HttpException::createFromCode(401);
            }

            $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
            $submission = $getSubmissionByIdResponse->Submission;

            if (!$user->getIsAdmin() && !$submission->getUserId()->equals($user->getId())) {
                throw HttpException::createFromCode(401);
            }

            $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest($submission->getProblemId()));
            $problem = $getProblemByIdResponse->Problem;

            $res->body(
                $this->Twig->render("Submission.twig", [
                    "submission" => $submission,
                    "problem" => $problem
                ])
            );
        } catch (SubmissionNotFoundException) {
            throw HttpException::createFromCode(404);
        }
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
     */
    public function getByProblem(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user)) {
            throw HttpException::createFromCode(401);
        }

        try {
            $users = [];
            if ($user->getIsAdmin()) {
                $getSubmissionsByProblemIdResponse = $this->GetSubmissionsByProblemIdUseCase->handle(new GetSubmissionsByProblemIdRequest(new ProblemId($req->problemId), intval($req->param("page", 1)), self::LimitPerPage));
                $submissions = $getSubmissionsByProblemIdResponse->Submissions;
                $count = $getSubmissionsByProblemIdResponse->Count;

                foreach ($submissions as $submission) {
                    $users[] = $this->GetUserByIdUseCase->handle(new GetUserByIdRequest($submission->getUserId()))->User;
                }
            } else {
                $getSubmissionsByProblemIdAndUserIdResponse = $this->GetSubmissionsByProblemIdAndUserIdUseCase->handle(new GetSubmissionsByProblemIdAndUserIdRequest(new ProblemId($req->problemId), $user->getId(), intval($req->param("page", 1)), self::LimitPerPage));
                $submissions = $getSubmissionsByProblemIdAndUserIdResponse->Submissions;
                $count = $getSubmissionsByProblemIdAndUserIdResponse->Count;
            }
        } catch (ProblemNotFoundException) {
            throw HttpException::createFromCode(404);
        }

        $res->body(
            $this->Twig->render("Problem/Submissions.twig", [
                "submissions" => $submissions,
                "page" => intval($req->param("page", 1)),
                "totalNumberOfSubmissions" => $count,
                "totalNumberOfPages" => intval(ceil($count / self::LimitPerPage)),
                "limitPerPage" => self::LimitPerPage,
                "users" => $users
            ])
        );
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
    public function getByUser(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user)) {
            throw HttpException::createFromCode(401);
        }

        $requestedUserId = new UserId($req->userId);

        if (!$user->getIsAdmin() && !$user->getId()->equals($requestedUserId)) {
            throw HttpException::createFromCode(401);
        }

        $getSubmissionsByUserIdResponse = $this->GetSubmissionsByUserIdUseCase->handle(new GetSubmissionsByUserIdRequest($requestedUserId, intval($req->param("page", 1)), self::LimitPerPage));
        $count = $getSubmissionsByUserIdResponse->Count;

        $submissions = $getSubmissionsByUserIdResponse->Submissions;
        $problems = [];
        foreach (array_reduce($submissions, function ($c, $e) {
            if (array_search($e->getProblemId(), $c) === false) {
                $c[] = $e->getProblemId();
            }
            return $c;
        }, []) as $problemId) {
            try {
                $getProblemByIdResponse = $this->GetProblemByIdUseCase->handle(new GetProblemByIdRequest($problemId));
                $problems[] = $getProblemByIdResponse->Problem;
            } catch (ProblemNotFoundException) {
                // The problem might be deleted during the rendering
                // ignored
            }
        }

        $res->body(
            $this->Twig->render("User/Submissions.twig", [
                "requestedUser" => $getSubmissionsByUserIdResponse->User,
                "submissions" => $submissions,
                "problems" => $problems,
                "page" => intval($req->param("page", 1)),
                "totalNumberOfSubmissions" => $count,
                "totalNumberOfPages" => intval(ceil($count / self::LimitPerPage)),
                "limitPerPage" => self::LimitPerPage
            ])
        );
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
        try {
            $user = $req->user;
            if (!isset($user) || !$user->getIsAdmin()) {
                throw HttpException::createFromCode(401);
            }

            $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
            $submission = $getSubmissionByIdResponse->Submission;

            $this->DeleteSubmissionUseCase->handle(new DeleteSubmissionRequest($submission->getId()));

            $res->redirect("/problem/" . $submission->getProblemId());
        } catch (SubmissionNotFoundException) {
            throw HttpException::createFromCode(404);
        }
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return void
     * @throws HttpException
     * @throws LockedResponseException
     * @throws ResponseAlreadySentException
     */
    public function handleDownload(Request $req, Response $res)
    {
        $user = $req->user;
        if (!isset($user)) {
            throw HttpException::createFromCode(401);
        }

        $getSubmissionByIdResponse = $this->GetSubmissionByIdUseCase->handle(new GetSubmissionByIdRequest(new SubmissionId($req->submissionId)));
        $submission = $getSubmissionByIdResponse->Submission;

        $sourceFilePath = $submission->getSourceFile()->getPath();
        if (!file_exists($sourceFilePath)) {
            throw HttpException::createFromCode(500);
        }

        $res->file($sourceFilePath, mimetype: "application/x-tar");
    }
}

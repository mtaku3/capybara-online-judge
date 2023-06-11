<?php

declare(strict_types=1);

namespace App\Application\PurgeSessions;

use App\Application\Session\ISessionRepository;
use DateTimeImmutable;
use Exception;

class PurgeSessionsUseCase
{
    /**
     * @var ISessionRepository
     */
    private readonly ISessionRepository $SessionRepository;

    /**
     * @param ISessionRepository $sessionRepository
     * @return void
     */
    public function __construct(ISessionRepository $sessionRepository)
    {
        $this->SessionRepository = $sessionRepository;
    }

    /**
     * @param PurgeSessionsRequest $request
     * @return PurgeSessionsResponse
     */
    public function handle(PurgeSessionsRequest $request): PurgeSessionsResponse
    {
        $sessions = $this->SessionRepository->findByUser($request->User);

        foreach ($sessions as $session) {
            if ($session->getExpiresAt() < (new DateTimeImmutable())) {
                $this->SessionRepository->delete($session);
            }
        }

        return new PurgeSessionsResponse();
    }
}

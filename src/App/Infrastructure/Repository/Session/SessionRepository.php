<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Session;

use App\Application\Session\Entity\Session;
use App\Application\Session\ISessionRepository;
use App\Domain\User\Entity\User;
use App\Application\Session\ValueObject\RefreshToken;
use App\Infrastructure\Repository\Session\Exception\SessionNotFoundException;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\ParserException;
use Cycle\ORM\Exception\LoaderException;
use Cycle\ORM\Select\Repository;

class SessionRepository implements ISessionRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var Repository
     */
    private readonly Repository $SessionRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Repository $sessionRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, Repository $sessionRepository)
    {
        $this->EntityManager = $entityManager;
        $this->SessionRepository = $sessionRepository;
    }

    /**
     * @param User $user
     * @param RefreshToken $refreshToken
     * @return Session
     * @throws SessionNotFoundException
     */
    public function findByUserAndRefreshToken(User $user, RefreshToken $refreshToken): Session
    {
        $session = $this->SessionRepository->findOne([
            "UserId" => $user->getId(),
            "RefreshToken" => $refreshToken
        ]);

        if (empty($session)) {
            throw new SessionNotFoundException();
        }

        return $session;
    }

    /**
     * @param User $user
     * @return Session[]
     * @throws ParserException
     * @throws LoaderException
     */
    public function findByUser(User $user): array
    {
        return iterator_to_array($this->SessionRepository->findAll(["UserId" => $user->getId()]));
    }

    /**
     * @param Session $session
     * @return void
     */
    public function save(Session $session): void
    {
        $this->EntityManager->persist($session);
        $this->EntityManager->run();
    }

    /**
     * @param Session $session
     * @return void
     */
    public function delete(Session $session): void
    {
        $this->EntityManager->delete($session);
        $this->EntityManager->run();
    }
}

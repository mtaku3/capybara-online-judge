<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\User;

use App\Domain\User\Entity\User;
use App\Domain\User\IUserRepository;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\User\Exception\UserNotFoundException;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Select\Repository;

class UserRepository implements IUserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private readonly EntityManagerInterface $EntityManager;
    /**
     * @var Repository
     */
    private readonly Repository $UserRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Repository $userRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $entityManager, Repository $userRepository)
    {
        $this->EntityManager = $entityManager;
        $this->UserRepository = $userRepository;
    }

    /**
     * @param UserId $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findById(UserId $id): User
    {
        $user = $this->UserRepository->findByPK($id);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function findByUsername(string $username): User
    {
        $user = $this->UserRepository->findOne([
            "Username" => $username
        ]);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $this->EntityManager->persist($user);
        $this->EntityManager->run();
    }
}

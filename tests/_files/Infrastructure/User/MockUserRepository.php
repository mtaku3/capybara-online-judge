<?php

declare(strict_types=1);

namespace Test\Infrastructure\User;

use App\Domain\User\IUserRepository;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Repository\User\Exception\UserNotFoundException;

class MockUserRepository implements IUserRepository
{
    /**
     * @var User[]
     */
    private array $records = [];

    /**
     * @param UserId $id
     * @return User
     */
    public function findById(UserId $id): User
    {
        $existingUser = current(array_filter($this->records, fn ($e) => $e->getId()->equals($id)));

        if ($existingUser !== false) {
            return $existingUser;
        } else {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param string $username
     * @return User
     */
    public function findByUsername(string $username): User
    {
        $existingUser = current(array_filter($this->records, fn ($e) => $e->getUsername() === $username));

        if ($existingUser !== false) {
            return $existingUser;
        } else {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $existingUser = current(array_filter($this->records, fn ($e) => $e->getId()->equals($user->getId())));

        if ($existingUser === false) {
            $this->records[] = $user;
        } else {
            $existingUser = $user;
        }
    }
}

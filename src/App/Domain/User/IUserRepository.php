<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;

interface IUserRepository
{
    /**
     * @param UserId $id
     * @return User
     */
    public function findById(UserId $id): User;

    /**
     * @param string $username
     * @return User
     */
    public function findByUsername(string $username): User;

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void;
}

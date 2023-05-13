<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;

interface IUserRepository
{
    public function findById(UserId $id): User;
    public function findByUsername(string $username): User;
    public function save(User $user): void;
}

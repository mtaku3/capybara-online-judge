<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\UserId;

class User
{
    private UserId $Id;
    private string $Username;
    private string $Password;
    private bool $IsAdmin;

    public function __construct(UserId $id, string $username, string $password, bool $isAdmin)
    {
        $this->Id = $id;
        $this->Username = $username;
        $this->Password = $password;
        $this->IsAdmin = $isAdmin;
    }

    public static function _create(string $username, string $password, bool $isAdmin): User
    {
        return new User(UserId::nextIdentity(), $username, self::hashPassword($password), $isAdmin);
    }

    private static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function getId(): UserId
    {
        return $this->Id;
    }

    public function getUsername(): string
    {
        return $this->Username;
    }

    public function comparePassword(string $password): bool
    {
        return password_verify($password, $this->Password);
    }

    public function hashAndSetPassword(string $password): void
    {
        $this->Password = self::hashPassword($password);
    }

    public function getIsAdmin(): bool
    {
        return $this->IsAdmin;
    }

    public function setIsAdmin(bool $value)
    {
        $this->IsAdmin = $value;
    }
}

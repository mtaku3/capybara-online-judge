<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\Exception\InvalidPasswordException;
use App\Domain\User\Exception\InvalidUsernameException;
use App\Domain\User\ValueObject\UserId;

class User
{
    private UserId $Id;
    private string $Username;
    private string $Password;
    private bool $IsAdmin;

    /**
     * @param UserId $id
     * @param string $username
     * @param string $password
     * @param bool $isAdmin
     * @return void
     */
    public function __construct(UserId $id, string $username, string $password, bool $isAdmin)
    {
        $this->Id = $id;
        $this->Username = $username;
        $this->Password = $password;
        $this->IsAdmin = $isAdmin;
    }

    /**
     * @param string $username
     * @param string $password
     * @param bool $isAdmin
     * @return User
     * @throws InvalidUsernameException
     * @throws InvalidPasswordException
     */
    public static function Create(string $username, string $password, bool $isAdmin): User
    {
        if (!self::ValidateUsername($username)) {
            throw new InvalidUsernameException();
        }

        if (!self::ValidatePassword($password)) {
            throw new InvalidPasswordException();
        }

        return new User(UserId::NextIdentity(), $username, self::HashPassword($password), $isAdmin);
    }

    /**
     * @param string $password
     * @return string
     */
    private static function HashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $username
     * @return bool
     */
    private static function ValidateUsername(string $username): bool
    {
        return boolval(preg_match("^[a-zA-Z0-9]{4,20}$", $username));
    }

    /**
     * @param string $password
     * @return bool
     */
    private static function ValidatePassword(string $password): bool
    {
        return boolval(preg_match("^[a-zA-Z0-9!@#$%^&*]{8,30}$", $password));
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
        if (!self::ValidatePassword($password)) {
            throw new InvalidPasswordException();
        }

        $this->Password = self::HashPassword($password);
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

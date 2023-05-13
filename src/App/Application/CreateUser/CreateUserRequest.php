<?php

declare(strict_types=1);

namespace App\Application\CreateUser;

class CreateUserRequest
{
    public readonly string $Username;
    public readonly string $Password;
    public readonly bool $IsAdmin;

    /**
     * @param string $username
     * @param string $password
     * @param bool $isAdmin
     * @return void
     */
    public function __construct(string $username, string $password, bool $isAdmin)
    {
        $this->Username = $username;
        $this->Password = $password;
        $this->IsAdmin = $isAdmin;
    }
}
